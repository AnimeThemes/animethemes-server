<?php

declare(strict_types=1);

namespace App\Actions\Models\Wiki\Anime\ApiAction;

use App\Actions\Models\Wiki\ApiAction;
use App\Enums\Models\Wiki\ResourceSite;
use App\Models\Wiki\Anime;
use App\Models\Wiki\ExternalResource;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Http;

/**
 * Class JikanAnimeApiAction.
 */
class JikanAnimeApiAction extends ApiAction
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
        $resource = $resources->firstWhere(ExternalResource::ATTRIBUTE_SITE, ResourceSite::ANILIST->value);

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

        if ($response = $this->response) {
            $links = Arr::get($response, 'data');

            foreach ($links as $link) {
                $siteMal = Arr::get($link, 'site');
                $url = Arr::get($link, 'url');

                foreach ($this->getResourcesMapping() as $site => $key) {
                    if ($siteMal === $key) {
                        $resources[$site] = $url;
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
    protected function getResourcesMapping(): array
    {
        return [
            ResourceSite::ANIDB->value => 'AniDB',
            ResourceSite::ANN->value => 'ANN',
            ResourceSite::OFFICIAL_SITE->value => 'Official Site',
        ];
    }

    /**
     * Get the mapping for the images.
     *
     * @return array<int, string>
     */
    protected function getImagesMapping(): array
    {
        return [];
    }
}