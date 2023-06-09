<?php

declare(strict_types=1);

namespace App\Actions\Models\Wiki\Anime\Resource;

use App\Actions\Models\Wiki\Anime\BackfillAnimeResourceAction;
use App\Enums\Models\Wiki\ResourceSite;
use App\Models\Wiki\ExternalResource;
use Illuminate\Http\Client\RequestException;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

/**
 * Class BackfillAnilistResourceAction.
 */
class BackfillAnilistResourceAction extends BackfillAnimeResourceAction
{
    /**
     * Get the site to backfill.
     *
     * @return ResourceSite
     */
    protected function getSite(): ResourceSite
    {
        return ResourceSite::ANILIST;
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
        // Allow fall-throughs in case Anilist Resource is not mapped to every external site.

        $malResource = $this->getModel()->resources()->firstWhere(ExternalResource::ATTRIBUTE_SITE, ResourceSite::MAL->value);
        if ($malResource instanceof ExternalResource) {
            $anilistResource = $this->getMalAnilistMapping($malResource);
            if ($anilistResource !== null) {
                return $anilistResource;
            }
        }

        $kitsuResource = $this->getModel()->resources()->firstWhere(ExternalResource::ATTRIBUTE_SITE, ResourceSite::KITSU->value);
        if ($kitsuResource instanceof ExternalResource) {
            $anilistResource = $this->getKitsuAnilistMapping($kitsuResource);
            if ($anilistResource !== null) {
                return $anilistResource;
            }
        }

        $anidbResource = $this->getModel()->resources()->firstWhere(ExternalResource::ATTRIBUTE_SITE, ResourceSite::ANIDB->value);
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
        if ($anilistId === null) {
            Log::info("Skipping null Anilist mapping for MAL id '$malResource->external_id'");

            return null;
        }

        return $this->getOrCreateResource($anilistId);
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

            return $this->getOrCreateResource(intval($externalId));
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
            return $this->getOrCreateResource($anilistId);
        }

        return null;
    }
}
