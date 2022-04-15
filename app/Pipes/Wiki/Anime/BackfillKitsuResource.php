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
 * Class BackfillKitsuResource.
 */
class BackfillKitsuResource extends BackfillAnimePipe
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
        $kitsuResource = $this->getKitsuResource();

        if ($kitsuResource !== null) {
            $this->attachKitsuResourceToAnime($kitsuResource);
        }

        if ($this->anime->resources()->where(ExternalResource::ATTRIBUTE_SITE, ResourceSite::KITSU)->doesntExist()) {
            $this->sendNotification($user, "Anime '{$this->anime->getName()}' has no Kitsu Resource after backfilling. Please review.");
        }

        return $next($user);
    }

    /**
     * Query third-party API for Kitsu Resource mapping.
     *
     * @return ExternalResource|null
     * @throws RequestException
     */
    protected function getKitsuResource(): ?ExternalResource
    {
        // Allow fall-throughs in case Kitsu Resource is not mapped to every external site.

        $malResource = $this->anime->resources()->firstWhere(ExternalResource::ATTRIBUTE_SITE, ResourceSite::MAL);
        if ($malResource instanceof ExternalResource) {
            $kitsuResource = $this->getKitsuMapping($malResource, 'MYANIMELIST_ANIME');
            if ($kitsuResource !== null) {
                return $kitsuResource;
            }

            // failed mapping, sleep before re-attempting
            sleep(rand(1, 3));
        }

        $anilistResource = $this->anime->resources()->firstWhere(ExternalResource::ATTRIBUTE_SITE, ResourceSite::ANILIST);
        if ($anilistResource instanceof ExternalResource) {
            $kitsuResource = $this->getKitsuMapping($anilistResource, 'ANILIST_ANIME');
            if ($kitsuResource !== null) {
                return $kitsuResource;
            }

            // failed mapping, sleep before re-attempting
            sleep(rand(1, 3));
        }

        $anidbResource = $this->anime->resources()->firstWhere(ExternalResource::ATTRIBUTE_SITE, ResourceSite::ANIDB);
        if ($anidbResource instanceof ExternalResource) {
            $kitsuResource = $this->getKitsuMapping($anidbResource, 'ANIDB');
            if ($kitsuResource !== null) {
                return $kitsuResource;
            }

            // failed mapping, sleep before re-attempting
            sleep(rand(1, 3));
        }

        $annResource = $this->anime->resources()->firstWhere(ExternalResource::ATTRIBUTE_SITE, ResourceSite::ANN);
        if ($annResource instanceof ExternalResource) {
            return $this->getKitsuMapping($annResource, 'ANIMENEWSNETWORK');
        }

        return null;
    }

    /**
     * Query Kitsu API for MAL mapping.
     *
     * @param  ExternalResource  $resource
     * @param  string  $externalSite
     * @return ExternalResource|null
     *
     * @throws RequestException
     */
    protected function getKitsuMapping(ExternalResource $resource, string $externalSite): ?ExternalResource
    {
        $query = '
        query ($externalId: ID!, $externalSite: MappingExternalSiteEnum!) {
            lookupMapping(externalId: $externalId, externalSite: $externalSite) {
                ... on Anime {
                    id
                    slug
                }
            }
        }
        ';

        $variables = [
            'externalId' => $resource->external_id,
            'externalSite' => $externalSite,
        ];

        $response = Http::post('https://kitsu.io/api/graphql', [
            'query' => $query,
            'variables' => $variables,
        ])
            ->throw()
            ->json();

        $kitsuMapping = Arr::get($response, 'data.lookupMapping');
        if ($kitsuMapping !== null) {
            $id = Arr::get($kitsuMapping, 'id');
            $slug = Arr::get($kitsuMapping, 'slug');
            if (empty($id) || empty($slug)) {
                Log::info("Skipping mapping of id '$id' and slug '$slug'");

                return null;
            }

            return $this->getOrCreateKitsuResource($id, $slug);
        }

        return null;
    }

    /**
     * Get or create Kitsu Resource from response.
     *
     * @param  string  $kitsuId
     * @param  string  $kitsuSlug
     * @return ExternalResource
     */
    protected function getOrCreateKitsuResource(string $kitsuId, string $kitsuSlug): ExternalResource
    {
        $kitsuResource = ExternalResource::query()
            ->select([ExternalResource::ATTRIBUTE_ID, ExternalResource::ATTRIBUTE_LINK])
            ->where(ExternalResource::ATTRIBUTE_SITE, ResourceSite::KITSU)
            ->where(ExternalResource::ATTRIBUTE_EXTERNAL_ID, $kitsuId)
            ->first();

        if ($kitsuResource === null) {
            Log::info("Creating kitsu resource '$kitsuId'");

            $kitsuResource = ExternalResource::factory()->createOne([
                ExternalResource::ATTRIBUTE_EXTERNAL_ID => $kitsuId,
                ExternalResource::ATTRIBUTE_LINK => ResourceSite::formatAnimeResourceLink(ResourceSite::KITSU(), intval($kitsuId), $kitsuSlug),
                ExternalResource::ATTRIBUTE_SITE => ResourceSite::KITSU,
            ]);
        }

        return $kitsuResource;
    }

    /**
     * Attach Kitsu Resource to Anime.
     *
     * @param  ExternalResource  $kitsuResource
     * @return void
     */
    protected function attachKitsuResourceToAnime(ExternalResource $kitsuResource): void
    {
        if (AnimeResource::query()
            ->where($this->anime->getKeyName(), $this->anime->getKey())
            ->where($kitsuResource->getKeyName(), $kitsuResource->getKey())
            ->doesntExist()
        ) {
            Log::info("Attaching resource '$kitsuResource->link' to anime '{$this->anime->getName()}'");
            $kitsuResource->anime()->attach($this->anime);
        }
    }
}
