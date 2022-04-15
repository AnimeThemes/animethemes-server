<?php

declare(strict_types=1);

namespace App\Pipes\Wiki\Anime;

use App\Enums\Models\Wiki\ResourceSite;
use App\Models\Auth\User;
use App\Models\Wiki\ExternalResource;
use App\Pivots\AnimeResource;
use Closure;
use Illuminate\Http\Client\RequestException;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

/**
 * Class BackfillAnidbResource.
 */
class BackfillAnidbResource extends BackfillAnimePipe
{
    /**
     * Handle an incoming request.
     *
     * @param  User  $user
     * @param  Closure  $next
     * @return mixed
     *
     * @throws RequestException
     */
    public function handle(User $user, Closure $next): mixed
    {
        $anidbResource = $this->getAnidbResource();

        if ($anidbResource !== null) {
            $this->attachAnidbResourceToAnime($anidbResource);
        }

        if ($this->anime->resources()->where(ExternalResource::ATTRIBUTE_SITE, ResourceSite::ANIDB)->doesntExist()) {
            $this->sendNotification($user, "Anime '{$this->anime->getName()}' has no AniDB Resource after backfilling. Please review.");
        }

        return $next($user);
    }

    /**
     * Query third-party API for AniDB Resource mapping.
     *
     * @return ExternalResource|null
     *
     * @throws RequestException
     */
    protected function getAnidbResource(): ?ExternalResource
    {
        // Allow fall-throughs in case AniDB Resource is not mapped to every external site.

        $malResource = $this->anime->resources()->firstWhere(ExternalResource::ATTRIBUTE_SITE, ResourceSite::MAL);
        if ($malResource instanceof ExternalResource) {
            $anidbResource = $this->getAnidbMapping($malResource, 'myanimelist');
            if ($anidbResource !== null) {
                return $anidbResource;
            }

            // failed mapping, sleep before re-attempting
            sleep(rand(1, 3));
        }

        $anilistResource = $this->anime->resources()->firstWhere(ExternalResource::ATTRIBUTE_SITE, ResourceSite::ANILIST);
        if ($anilistResource instanceof ExternalResource) {
            $anidbResource = $this->getAnidbMapping($anilistResource, 'anilist');
            if ($anidbResource !== null) {
                return $anidbResource;
            }

            // failed mapping, sleep before re-attempting
            sleep(rand(1, 3));
        }

        $kitsuResource = $this->anime->resources()->firstWhere(ExternalResource::ATTRIBUTE_SITE, ResourceSite::KITSU);
        if ($kitsuResource instanceof ExternalResource) {
            return $this->getAnidbMapping($kitsuResource, 'kitsu');
        }

        return null;
    }

    /**
     * Query Yuna API for AniDB mapping.
     *
     * @param  ExternalResource  $resource
     * @param  string  $source
     * @return ExternalResource|null
     *
     * @throws RequestException
     */
    protected function getAnidbMapping(ExternalResource $resource, string $source): ?ExternalResource
    {
        $response = Http::get('https://relations.yuna.moe/api/ids', [
            'source' => $source,
            'id' => $resource->external_id,
        ])
            ->throw()
            ->json();

        $anidbId = Arr::get($response, 'anidb');

        // Only proceed if we have a match
        if ($anidbId !== null) {
            return $this->getOrCreateAnidbResource($anidbId);
        }

        return null;
    }

    /**
     * Get or create AniDB Resource from response.
     *
     * @param  int  $anidbId
     * @return ExternalResource
     */
    protected function getOrCreateAnidbResource(int $anidbId): ExternalResource
    {
        $anidbResource = ExternalResource::query()
            ->select([ExternalResource::ATTRIBUTE_ID, ExternalResource::ATTRIBUTE_LINK])
            ->where(ExternalResource::ATTRIBUTE_SITE, ResourceSite::ANIDB)
            ->where(ExternalResource::ATTRIBUTE_EXTERNAL_ID, $anidbId)
            ->first();

        // Create AniDB resource if it doesn't already exist
        if ($anidbResource === null) {
            Log::info("Creating anidb resource '$anidbId'");

            $anidbResource = ExternalResource::factory()->createOne([
                ExternalResource::ATTRIBUTE_EXTERNAL_ID => $anidbId,
                ExternalResource::ATTRIBUTE_LINK => ResourceSite::formatAnimeResourceLink(ResourceSite::ANIDB(), $anidbId),
                ExternalResource::ATTRIBUTE_SITE => ResourceSite::ANIDB,
            ]);
        }

        return $anidbResource;
    }

    /**
     * Attach AniDB Resource to Anime.
     *
     * @param  ExternalResource  $anidbResource
     * @return void
     */
    protected function attachAnidbResourceToAnime(ExternalResource $anidbResource): void
    {
        if (AnimeResource::query()
            ->where($this->anime->getKeyName(), $this->anime->getKey())
            ->where($anidbResource->getKeyName(), $anidbResource->getKey())
            ->doesntExist()
        ) {
            Log::info("Attaching resource '$anidbResource->link' to anime '{$this->anime->getName()}'");
            $anidbResource->anime()->attach($this->anime);
        }
    }
}
