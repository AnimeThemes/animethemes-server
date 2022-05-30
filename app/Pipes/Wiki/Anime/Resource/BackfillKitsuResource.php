<?php

declare(strict_types=1);

namespace App\Pipes\Wiki\Anime\Resource;

use App\Enums\Models\Wiki\ResourceSite;
use App\Models\Wiki\ExternalResource;
use App\Pipes\Wiki\Anime\BackfillAnimeResource;
use Illuminate\Http\Client\RequestException;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

/**
 * Class BackfillKitsuResource.
 */
class BackfillKitsuResource extends BackfillAnimeResource
{
    /**
     * Get the site to backfill.
     *
     * @return ResourceSite
     */
    protected function getSite(): ResourceSite
    {
        return ResourceSite::KITSU();
    }

    /**
     * Query third-party APIs to find Resource mapping.
     *
     * @return ExternalResource|null
     *
     * @throws RequestException
     */
    protected function getResource(): ?ExternalResource
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

            return $this->getOrCreateResource(intval($id), $slug);
        }

        return null;
    }
}
