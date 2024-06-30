<?php

declare(strict_types=1);

namespace App\Actions\Models\Wiki\Anime;

use App\Actions\ActionResult;
use App\Actions\Models\BackfillAction;
use App\Enums\Actions\ActionStatus;
use App\Enums\Models\Wiki\AnimeSynonymType;
use App\Enums\Models\Wiki\ResourceSite;
use App\Models\Wiki\Anime;
use App\Models\Wiki\Anime\AnimeSynonym;
use App\Models\Wiki\ExternalResource;
use Exception;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

/**
 * Class BackfillAnimeSynonymsAction.
 *
 * @extends BackfillAction<Anime>
 */
class BackfillAnimeSynonymsAction extends BackfillAction
{
    /**
     * Create a new action instance.
     *
     * @param  Anime  $anime
     */
    public function __construct(Anime $anime)
    {
        parent::__construct($anime);
    }

    /**
     * Handle action.
     *
     * @return ActionResult
     *
     * @throws Exception
     */
    public function handle(): ActionResult
    {
        try {
            DB::beginTransaction();

            $titles = $this->getTitles();

            if ($titles === null) {
                DB::rollBack();
                return new ActionResult(ActionStatus::FAILED);
            }

            foreach ($titles as $type => $text) {
                if (
                    $text === null
                    || empty($text)
                    || ($type === 'romaji' && $text === $this->getModel()->getName())
                ) continue;

                Log::info("Creating {$text}");

                AnimeSynonym::query()->create([
                    AnimeSynonym::ATTRIBUTE_TEXT => $text,
                    AnimeSynonym::ATTRIBUTE_TYPE => static::getAnilistSynonymsMap($type)->value,
                    AnimeSynonym::ATTRIBUTE_ANIME => $this->getModel()->getKey(),
                ]);
            }

            DB::commit();
        } catch (Exception $e) {
            Log::error($e->getMessage());

            DB::rollBack();

            throw $e;
        }

        return new ActionResult(ActionStatus::PASSED);
    }

    /**
     * Get the enum related to the array map.
     *
     * @param  string  $title
     * @return AnimeSynonymType
     */
    protected static function getAnilistSynonymsMap($title): AnimeSynonymType
    {
        return match ($title) {
            'english' => AnimeSynonymType::ENGLISH,
            'native' => AnimeSynonymType::NATIVE,
            default => AnimeSynonymType::OTHER,
        };
    }

    /**
     * Get the Anilist Resource.
     *
     * @return ExternalResource|null
     */
    protected function getAnilistResource(): ?ExternalResource
    {
        $anilistResource = $this->getModel()->resources()->firstWhere(ExternalResource::ATTRIBUTE_SITE, ResourceSite::ANILIST->value);
        if ($anilistResource instanceof ExternalResource) {
            return $anilistResource;
        }

        return null;
    }

    /**
     * Get the titles by AniList API.
     * 
     * @return array|null
     */
    protected function getTitles(): ?array
    {
        $anilistResource = $this->getAnilistResource();

        if ($anilistResource !== null) {
            $query = '
            query ($id: Int) {
                Media (id: $id, type: ANIME) {
                    title {
                        romaji
                        english
                        native
                    }
                }
            }
            ';

            $variables = [
                'id' => $anilistResource->external_id,
            ];

            $response = Http::post('https://graphql.anilist.co', [
                'query' => $query,
                'variables' => $variables,
            ])
                ->throw()
                ->json();

            $titles = Arr::get($response, 'data.Media.title');

            return $titles;
        }

        return null;
    }

    /**
     * Get the model the action is handling.
     *
     * @return Anime
     */
    protected function getModel(): Anime
    {
        return $this->model;
    }

    /**
     * Get the relation to resources.
     *
     * @return HasMany
     */
    protected function relation(): HasMany
    {
        return $this->getModel()->animesynonyms();
    }
}
