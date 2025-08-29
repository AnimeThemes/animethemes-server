<?php

declare(strict_types=1);

namespace App\Actions\Models\Wiki;

use App\Actions\ActionResult;
use App\Actions\Models\BackfillAction;
use App\Concerns\Models\CanCreateExternalResource;
use App\Enums\Actions\ActionStatus;
use App\Enums\Models\Wiki\ResourceSite;
use App\Models\Wiki\Song;
use Exception;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Uri;

/**
 * @extends BackfillAction<Song>
 */
class BackfillSongAction extends BackfillAction
{
    use CanCreateExternalResource;

    public function __construct(
        Song $song,
        protected readonly string $lnkto,
    ) {
        parent::__construct($song);
    }

    /**
     * Handle action.
     *
     * @throws Exception
     */
    public function handle(): ActionResult
    {
        try {
            DB::beginTransaction();

            // Request to lnk.to site
            $response = Http::get($this->lnkto)
                ->throw();

            $pattern = '/<a[^>]*class="[^"]*music-service-list__link[^"]*js-redirect[^"]*"[^>]*href="([^"]+)"[^>]*data-label="([^"]*)"[^>]*>/i';

            preg_match_all($pattern, $response->body(), $matches);

            foreach ($matches[1] as $key => $link) {
                $label = $matches[2][$key];
                $resourceSite = $this->getMappingFromExternalSite($label);

                if (! $resourceSite) {
                    Log::info("Skipping {$label} for Song {$this->getModel()->getName()}");
                    continue;
                }

                $this->createResource(Uri::of($link), $resourceSite, $this->getModel());
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
     * Get the resource site according the external label.
     */
    protected function getMappingFromExternalSite(string $label): ?ResourceSite
    {
        return match ($label) {
            'applemusic' => ResourceSite::APPLE_MUSIC,
            'spotify' => ResourceSite::SPOTIFY,
            'youtubemusic' => ResourceSite::YOUTUBE_MUSIC,
            'amazonmp3' => ResourceSite::AMAZON_MUSIC,
            default => null,
        };
    }

    /**
     * Get the model the action is handling.
     */
    protected function getModel(): Song
    {
        return $this->model;
    }

    /**
     * Get the relation to resources.
     *
     * @return BelongsToMany
     */
    protected function relation(): BelongsToMany
    {
        return $this->getModel()->resources();
    }
}
