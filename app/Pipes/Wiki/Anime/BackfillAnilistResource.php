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
 * Class BackfillAnilistResource.
 */
class BackfillAnilistResource extends BackfillAnimePipe
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
        $anilistResource = $this->getAnilistResource();

        if ($anilistResource !== null) {
            $this->attachAnilistResourceToAnime($anilistResource);
        }

        if ($this->anime->resources()->where(ExternalResource::ATTRIBUTE_SITE, ResourceSite::ANILIST)->doesntExist()) {
            $this->sendNotification($user, "Anime '{$this->anime->getName()}' has no Anilist Resource after backfilling. Please review.");
        }

        return $next($user);
    }

    /**
     * Query third-party API for Anilist Resource mapping.
     *
     * @return ExternalResource|null
     *
     * @throws RequestException
     */
    protected function getAnilistResource(): ?ExternalResource
    {
        // Allow fall-throughs in case Anilist Resource is not mapped to every external site.

        $malResource = $this->anime->resources()->firstWhere(ExternalResource::ATTRIBUTE_SITE, ResourceSite::MAL);
        if ($malResource instanceof ExternalResource) {
            $anilistResource = $this->getMalAnilistMapping($malResource);
            if ($anilistResource !== null) {
                return $anilistResource;
            }
        }

        $kitsuResource = $this->anime->resources()->firstWhere(ExternalResource::ATTRIBUTE_SITE, ResourceSite::KITSU);
        if ($kitsuResource instanceof ExternalResource) {
            $anilistResource = $this->getKitsuAnilistMapping($kitsuResource);
            if ($anilistResource !== null) {
                return $anilistResource;
            }
        }

        $anidbResource = $this->anime->resources()->firstWhere(ExternalResource::ATTRIBUTE_SITE, ResourceSite::ANIDB);
        if ($anidbResource instanceof ExternalResource) {
            return $this->getAnidbAnilistMapping($anidbResource);
        }

        return null;
    }

    /**
     * Query Anilist API for MAL mapping.
     *
     * @param  ExternalResource  $malResource
     * @return ExternalResource|null
     *
     * @throws RequestException
     */
    protected function getMalAnilistMapping(ExternalResource $malResource): ?ExternalResource
    {
        $query = '
        query ($id: Int) {
            Media (idMal: $id, type: ANIME) {
                id
            }
        }
        ';

        $variables = [
            'id' => $malResource->external_id,
        ];

        $response = Http::post('https://graphql.anilist.co', [
            'query' => $query,
            'variables' => $variables,
        ])
            ->throw()
            ->json();

        $anilistId = Arr::get($response, 'data.Media.id');
        if ($anilistId == null) {
            Log::info("Skipping null Anilist mapping for MAL id '$malResource->external_id'");

            return null;
        }

        return $this->getOrCreateAnilistResource($anilistId);
    }

    /**
     * Query Kitsu API for Anilist mapping.
     *
     * @param  ExternalResource  $kitsuResource
     * @return ExternalResource|null
     *
     * @throws RequestException
     */
    protected function getKitsuAnilistMapping(ExternalResource $kitsuResource): ?ExternalResource
    {
        $query = '
        query ($id: ID!) {
            findAnimeById (id: $id) {
                mappings(first:20) {
                    nodes {
                        externalId
                        externalSite
                    }
                }
            }
        }
        ';

        $variables = [
            'id' => $kitsuResource->external_id,
        ];

        $response = Http::post('https://kitsu.io/api/graphql', [
            'query' => $query,
            'variables' => $variables,
        ])
            ->throw()
            ->json();

        $kitsuMappings = Arr::get($response, 'data.findAnimeById.mappings.nodes', []);
        foreach ($kitsuMappings as $kitsuMapping) {
            $externalId = Arr::get($kitsuMapping, 'externalId');
            $externalSite = Arr::get($kitsuMapping, 'externalSite');
            if ($externalSite !== 'ANILIST_ANIME' || empty($externalId)) {
                Log::info("Skipping mapping of externalId '$externalId' and externalSite '$externalSite'");
                continue;
            }

            return $this->getOrCreateAnilistResource(intval($externalId));
        }

        return null;
    }

    /**
     * Query Yuna API for Anilist mapping.
     *
     * @param  ExternalResource  $anidbResource
     * @return ExternalResource|null
     *
     * @throws RequestException
     */
    protected function getAnidbAnilistMapping(ExternalResource $anidbResource): ?ExternalResource
    {
        $response = Http::get('https://relations.yuna.moe/api/ids', [
            'source' => 'anidb',
            'id' => $anidbResource->external_id,
        ])
            ->throw()
            ->json();

        $anilistId = Arr::get($response, 'anilist');

        if ($anilistId !== null) {
            return $this->getOrCreateAnilistResource($anilistId);
        }

        return null;
    }

    /**
     * Get or create Anilist Resource from response.
     *
     * @param  int  $anilistId
     * @return ExternalResource
     */
    protected function getOrCreateAnilistResource(int $anilistId): ExternalResource
    {
        $anilistResource = ExternalResource::query()
            ->select([ExternalResource::ATTRIBUTE_ID, ExternalResource::ATTRIBUTE_LINK])
            ->where(ExternalResource::ATTRIBUTE_SITE, ResourceSite::ANILIST)
            ->where(ExternalResource::ATTRIBUTE_EXTERNAL_ID, $anilistId)
            ->first();

        if ($anilistResource === null) {
            Log::info("Creating anilist resource '$anilistId'");

            $anilistResource = ExternalResource::factory()->createOne([
                ExternalResource::ATTRIBUTE_EXTERNAL_ID => $anilistId,
                ExternalResource::ATTRIBUTE_LINK => ResourceSite::formatAnimeResourceLink(ResourceSite::ANILIST(), $anilistId),
                ExternalResource::ATTRIBUTE_SITE => ResourceSite::ANILIST,
            ]);
        }

        return $anilistResource;
    }

    /**
     * Attach Anilist Resource to Anime.
     *
     * @param  ExternalResource  $anilistResource
     * @return void
     */
    protected function attachAnilistResourceToAnime(ExternalResource $anilistResource): void
    {
        if (AnimeResource::query()
            ->where($this->anime->getKeyName(), $this->anime->getKey())
            ->where($anilistResource->getKeyName(), $anilistResource->getKey())
            ->doesntExist()
        ) {
            Log::info("Attaching resource '$anilistResource->link' to anime '{$this->anime->getName()}'");
            $anilistResource->anime()->attach($this->anime);
        }
    }
}
