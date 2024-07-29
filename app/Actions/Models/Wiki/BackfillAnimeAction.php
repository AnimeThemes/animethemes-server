<?php

declare(strict_types=1);

namespace App\Actions\Models\Wiki;

use App\Actions\ActionResult;
use App\Actions\Models\BackfillWikiAction;
use App\Actions\Models\Wiki\Anime\ApiAction\AnilistAnimeApiAction;
use App\Actions\Models\Wiki\Anime\ApiAction\JikanAnimeApiAction;
use App\Actions\Models\Wiki\Anime\ApiAction\LivechartAnimeApiAction;
use App\Actions\Models\Wiki\Anime\ApiAction\MalAnimeApiAction;
use App\Concerns\Models\CanCreateStudio;
use App\Enums\Actions\ActionStatus;
use App\Enums\Models\Wiki\AnimeSynonymType;
use App\Models\Wiki\Anime;
use App\Models\Wiki\Anime\AnimeSynonym;
use Exception;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

/**
 * Class BackfillAnimeAction.
 */
class BackfillAnimeAction extends BackfillWikiAction
{
    use CanCreateStudio;

    final public const STUDIOS = 'studios';
    final public const SYNONYMS = 'synonyms';

    /**
     * Create a new action instance.
     *
     * @param  Anime  $anime
     * @param  array  $toBackfill
     */
    public function __construct(protected Anime $anime, protected array $toBackfill)
    {
        parent::__construct($anime, $toBackfill);
    }

    /**
     * Handle the action.
     *
     * @return ActionResult
     */
    public function handle(): ActionResult
    {
        try {
            foreach ($this->getApis() as $api) {
                DB::beginTransaction();

                if (
                    count($this->toBackfill[self::RESOURCES]) === 0
                    && count($this->toBackfill[self::IMAGES]) === 0
                    && !$this->toBackfill[self::STUDIOS]
                    && !$this->toBackfill[self::SYNONYMS]
                ) {
                    // Don't make other requests if everything is backfilled
                    Log::info("Backfill action finished for Anime {$this->getModel()->getName()}");
                    DB::rollBack();
                    break;
                }

                $response = $api->handle($this->getModel()->resources());

                $this->forResources($response);
                $this->forImages($response);
                $this->forStudios($response);
                $this->forSynonyms($response);

                Log::info("Commit try for api {$api->getSite()->localize()}");
                DB::commit();
            }
        } catch (Exception $e) {
            Log::error($e->getMessage());

            DB::rollBack();
        }

        return new ActionResult(ActionStatus::PASSED);
    }

    /**
     * Get the api actions available for the backfill action.
     *
     * @return array<ApiAction>
     */
    protected function getApis(): array
    {
        return [
            new LivechartAnimeApiAction(),
            new AnilistAnimeApiAction(),
            new MalAnimeApiAction(),
            //new JikanAnimeApiAction(),
        ];
    }

    /**
     * Create the studios given the response.
     *
     * @param  ApiAction  $response
     * @return void
     */
    protected function forStudios(ApiAction $response): void
    {
        $studios = $response->getStudios();

        if (!$this->toBackfill[self::STUDIOS]) return;

        foreach ($studios as $studio) {
            $id = Arr::get($studio, 'id');
            $name = Arr::get($studio, 'name');

            if (empty($name) || empty($id)) {
                Log::info("Skipping empty studio of name '$name' and id '$id''");
                continue;
            }

            $studio = $this->getOrCreateStudio($name);

            Log::info("Attaching Studio of name '$name' to Anime {$this->getModel()->getName()}");
            $this->getModel()->studios()->attach($studio);

            $this->toBackfill[self::STUDIOS] = false;

            $this->ensureStudioHasResource($studio, $response->getSite(), $id);
        }
    }

    /**
     * Create the synonyms given the response.
     *
     * @param  ApiAction  $api
     * @return void
     */
    protected function forSynonyms(ApiAction $api): void
    {
        if (!$this->toBackfill[self::SYNONYMS]) return;

        foreach ($api->getSynonyms() as $type => $text) {
            if (
                $text === null
                || empty($text)
                || ($type === AnimeSynonymType::OTHER->value && $text === $this->getModel()->getName())
            ) continue;

            Log::info("Creating {$text}");

            AnimeSynonym::query()->create([
                AnimeSynonym::ATTRIBUTE_TEXT => $text,
                AnimeSynonym::ATTRIBUTE_TYPE => $type,
                AnimeSynonym::ATTRIBUTE_ANIME => $this->getModel()->getKey(),
            ]);

            $this->toBackfill[self::SYNONYMS] = false;
        }
    }

    /**
     * Get the model for the action.
     *
     * @return Anime
     */
    protected function getModel(): Anime
    {
        return $this->anime;
    }
}
