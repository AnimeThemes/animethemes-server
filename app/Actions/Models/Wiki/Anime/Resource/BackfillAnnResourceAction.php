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
 * Class BackfillAnnResourceAction.
 */
class BackfillAnnResourceAction extends BackfillAnimeResourceAction
{
    /**
     * Get the site to backfill.
     *
     * @return ResourceSite
     */
    protected function getSite(): ResourceSite
    {
        return ResourceSite::ANN;
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
        $malResource = $this->getModel()->resources()->firstWhere(ExternalResource::ATTRIBUTE_SITE, ResourceSite::MAL->value);
        if ($malResource instanceof ExternalResource) {
            return $this->getMalAnnMapping($malResource);
        }

        $kitsuResource = $this->getModel()->resources()->firstWhere(ExternalResource::ATTRIBUTE_SITE, ResourceSite::KITSU->value);
        if ($kitsuResource instanceof ExternalResource) {
            return $this->getKitsuAnnMapping($kitsuResource);
        }

        return null;
    }

    /**
     * Query Jikan Unofficial MAL API for ANN mapping.
     *
     * @param  ExternalResource  $malResource
     * @return ExternalResource|null
     *
     * @throws RequestException
     */
    protected function getMalAnnMapping(ExternalResource $malResource): ?ExternalResource
    {
        $id = $malResource->external_id;

        $response = Http::get("https://api.jikan.moe/v4/anime/$id/external")
            ->throw()
            ->json();

        $malMappings = Arr::get($response, 'data');

        foreach ($malMappings as $malMapping) {
            $name = Arr::get($malMapping, 'name');

            if ($name !== 'ANN') {
                Log::info("Skipping mapping of externalSite '$name'");
                continue;
            }

            $url = Arr::get($malMapping, 'url');
            $externalId = intval($this->getSite()::parseIdFromLink($url));

            return $this->getOrCreateResource($externalId);
        }

        return null;
    }

    /**
     * Query Kitsu API for ANN mapping.
     *
     * @param  ExternalResource  $kitsuResource
     * @return ExternalResource|null
     *
     * @throws RequestException
     */
    protected function getKitsuAnnMapping(ExternalResource $kitsuResource): ?ExternalResource
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
            if ($externalSite !== 'ANIMENEWSNETWORK' || empty($externalId)) {
                Log::info("Skipping mapping of externalId '$externalId' and externalSite '$externalSite'");
                continue;
            }

            return $this->getOrCreateResource(intval($externalId));
        }

        return null;
    }
}
