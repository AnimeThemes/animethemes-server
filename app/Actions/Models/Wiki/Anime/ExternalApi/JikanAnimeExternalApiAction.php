<?php

declare(strict_types=1);

namespace App\Actions\Models\Wiki\Anime\ExternalApi;

use App\Actions\Models\Wiki\ExternalApiAction;
use App\Contracts\Actions\Models\Wiki\BackfillResources;
use App\Enums\Models\Wiki\ResourceSite;
use App\Models\Wiki\Anime;
use App\Models\Wiki\ExternalResource;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Http;

/**
 * Class JikanAnimeExternalApiAction.
 */
class JikanAnimeExternalApiAction extends ExternalApiAction implements BackfillResources
{
    /**
     * Get the site to backfill.
     *
     * @return ResourceSite
     */
    public function getSite(): ResourceSite
    {
        return ResourceSite::MAL;
    }

    /**
     * Set the response after the request.
     *
     * @param  BelongsToMany<ExternalResource, Anime>  $resources
     * @return static
     */
    public function handle(BelongsToMany $resources): static
    {
        $resource = $resources->firstWhere(ExternalResource::ATTRIBUTE_SITE, ResourceSite::MAL->value);

        if ($resource instanceof ExternalResource) {
            $id = $resource->external_id;

            $response = Http::get("https://api.jikan.moe/v4/anime/$id/external")
                ->throw()
                ->json();

            $this->response = $response;
        }

        return $this;
    }

    /**
     * Get the mapped resources.
     *
     * @return array<int, string>
     */
    public function getResources(): array
    {
        $resources = [];

        $reversed = array_flip($this->getResourcesMapping());

        if ($response = $this->response) {
            $links = Arr::get($response, 'data');

            foreach ($links as $link) {
                $siteMal = Arr::get($link, 'name');
                $url = Arr::get($link, 'url');

                foreach ($this->getResourcesMapping() as $site => $key) {
                    if ($siteMal === $key) {
                        $resourceSite = ResourceSite::from(Arr::get($reversed, $siteMal));
                        $resources[$site] = $resourceSite->formatResourceLink(Anime::class, intval(ResourceSite::parseIdFromLink($url)));
                    }
                }
            }
        }

        return $resources;
    }

    /**
     * Get the available sites to backfill.
     *
     * @return array
     */
    public function getResourcesMapping(): array
    {
        return [
            ResourceSite::ANIDB->value => 'AniDB',
            ResourceSite::ANN->value => 'ANN',
        ];
    }
}
