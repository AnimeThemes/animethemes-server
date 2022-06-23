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
 * Class BackfillMalResource.
 */
class BackfillMalResource extends BackfillAnimeResource
{
    /**
     * Get the site to backfill.
     *
     * @return ResourceSite
     */
    protected function getSite(): ResourceSite
    {
        return ResourceSite::MAL();
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
        $kitsuResource = $this->getModel()->resources()->firstWhere(ExternalResource::ATTRIBUTE_SITE, ResourceSite::KITSU);
        if ($kitsuResource instanceof ExternalResource) {
            $malResource = $this->getKitsuMalMapping($kitsuResource);
            if ($malResource !== null) {
                return $malResource;
            }
        }

        $anilistResource = $this->getModel()->resources()->firstWhere(ExternalResource::ATTRIBUTE_SITE, ResourceSite::ANILIST);
        if ($anilistResource instanceof ExternalResource) {
            $malResource = $this->getAnilistMalMapping($anilistResource);
            if ($malResource !== null) {
                return $malResource;
            }
        }

        $anidbResource = $this->getModel()->resources()->firstWhere(ExternalResource::ATTRIBUTE_SITE, ResourceSite::ANIDB);
        if ($anidbResource instanceof ExternalResource) {
            return $this->getAnidbMalMapping($anidbResource);
        }

        return null;
    }

    /**
     * Query Kitsu API for MAL mapping.
     *
     * @param  ExternalResource  $kitsuResource
     * @return ExternalResource|null
     *
     * @throws RequestException
     */
    protected function getKitsuMalMapping(ExternalResource $kitsuResource): ?ExternalResource
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
            if ($externalSite !== 'MYANIMELIST_ANIME' || empty($externalId)) {
                Log::info("Skipping mapping of externalId '$externalId' and externalSite '$externalSite'");
                continue;
            }

            return $this->getOrCreateResource(intval($externalId));
        }

        return null;
    }

    /**
     * Query Anilist API for MAL mapping.
     *
     * @param  ExternalResource  $anilistResource
     * @return ExternalResource|null
     *
     * @throws RequestException
     */
    protected function getAnilistMalMapping(ExternalResource $anilistResource): ?ExternalResource
    {
        $query = '
        query ($id: Int) {
            Media (id: $id, type: ANIME) {
                idMal
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

        $malId = Arr::get($response, 'data.Media.idMal');
        if ($malId === null) {
            Log::info("Skipping null MAL mapping for Anilist id '$anilistResource->external_id'");

            return null;
        }

        return $this->getOrCreateResource($malId);
    }

    /**
     * Query Yuna API for Mal mapping.
     *
     * @param  ExternalResource  $anidbResource
     * @return ExternalResource|null
     *
     * @throws RequestException
     */
    protected function getAnidbMalMapping(ExternalResource $anidbResource): ?ExternalResource
    {
        $response = Http::get('https://relations.yuna.moe/api/ids', [
            'source' => 'anidb',
            'id' => $anidbResource->external_id,
        ])
            ->throw()
            ->json();

        $malId = Arr::get($response, 'myanimelist');

        if ($malId !== null) {
            return $this->getOrCreateResource($malId);
        }

        return null;
    }
}
