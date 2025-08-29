<?php

declare(strict_types=1);

namespace App\Actions\Models\Wiki;

use App\Actions\ActionResult;
use App\Actions\Models\BackfillWikiAction;
use App\Actions\Models\Wiki\Anime\ExternalApi\AnilistAnimeExternalApiAction;
use App\Actions\Models\Wiki\Anime\ExternalApi\JikanAnimeExternalApiAction;
use App\Actions\Models\Wiki\Anime\ExternalApi\LivechartAnimeExternalApiAction;
use App\Actions\Models\Wiki\Anime\ExternalApi\MalAnimeExternalApiAction;
use App\Concerns\Models\CanCreateAnimeSynonym;
use App\Concerns\Models\CanCreateStudio;
use App\Contracts\Actions\Models\Wiki\BackfillImages;
use App\Contracts\Actions\Models\Wiki\BackfillResources;
use App\Contracts\Actions\Models\Wiki\BackfillStudios;
use App\Contracts\Actions\Models\Wiki\BackfillSynonyms;
use App\Enums\Actions\ActionStatus;
use App\Enums\Models\Wiki\AnimeSynonymType;
use App\Models\Wiki\Anime;
use Exception;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class BackfillAnimeAction extends BackfillWikiAction
{
    use CanCreateAnimeSynonym;
    use CanCreateStudio;

    final public const STUDIOS = 'studios';
    final public const SYNONYMS = 'synonyms';

    /**
     * @param  array  $toBackfill
     */
    public function __construct(protected Anime $anime, protected array $toBackfill)
    {
        parent::__construct($anime, $toBackfill);
    }

    public function handle(): ActionResult
    {
        try {
            foreach ($this->getExternalApiActions() as $api) {
                DB::beginTransaction();

                if (
                    count($this->toBackfill[self::RESOURCES]) === 0
                    && count($this->toBackfill[self::IMAGES]) === 0
                    && ! $this->toBackfill[self::STUDIOS]
                    && ! $this->toBackfill[self::SYNONYMS]
                ) {
                    // Don't make other requests if everything is backfilled
                    Log::info("Backfill action finished for Anime {$this->getModel()->getName()}");
                    DB::rollBack();
                    break;
                }

                $response = $api->handle($this->getModel()->resources());

                if ($response instanceof BackfillResources) {
                    $this->forResources($response);
                }

                if ($response instanceof BackfillImages) {
                    $this->forImages($response);
                }

                if ($response instanceof BackfillStudios) {
                    $this->forStudios($response);
                }

                if ($response instanceof BackfillSynonyms) {
                    $this->forSynonyms($response);
                }

                DB::commit();
            }
        } catch (Exception $e) {
            Log::error($e->getMessage());

            DB::rollBack();

            throw $e;
        }

        return new ActionResult(ActionStatus::PASSED);
    }

    /**
     * Get the external API actions available for the backfill action.
     *
     * @return ExternalApiAction[]
     */
    protected function getExternalApiActions(): array
    {
        return [
            new LivechartAnimeExternalApiAction(),
            new AnilistAnimeExternalApiAction(),
            new JikanAnimeExternalApiAction(),
            new MalAnimeExternalApiAction(),
        ];
    }

    /**
     * Create the studios given the response.
     */
    protected function forStudios(ExternalApiAction&BackfillStudios $response): void
    {
        $studios = $response->getStudios();

        if (! $this->toBackfill[self::STUDIOS]) {
            return;
        }

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

            $this->ensureStudioHasResource($studio, $response->getSite(), $id);
        }

        if ($this->getModel()->studios()->exists()) {
            $this->toBackfill[self::STUDIOS] = false;
        }
    }

    /**
     * Create the synonyms given the response.
     */
    protected function forSynonyms(ExternalApiAction&BackfillSynonyms $api): void
    {
        if (! $this->toBackfill[self::SYNONYMS]) {
            return;
        }

        $texts = [];
        foreach ($api->getSynonyms() as $type => $text) {
            if ($type === AnimeSynonymType::OTHER->value && in_array($text, $texts)) {
                Log::info("Skipping duplicate synonym '$text' for Anime {$this->getModel()->getName()}");
                continue;
            }

            $this->createAnimeSynonym($text, $type, $this->getModel());
            $texts[] = $text;
        }

        if ($this->getModel()->animesynonyms()->exists()) {
            $this->toBackfill[self::SYNONYMS] = false;
        }
    }

    protected function getModel(): Anime
    {
        return $this->anime;
    }
}
