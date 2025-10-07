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

class AniZipAnimeExternalApiAction extends ExternalApiAction implements BackfillResources
{
    public function getSite(): ResourceSite
    {
        return ResourceSite::ANILIST;
    }

    /**
     * Set the response after the request.
     *
     * @param  BelongsToMany<ExternalResource, Anime>  $resources
     */
    public function handle(BelongsToMany $resources): static
    {
        $resource = $resources->firstWhere(ExternalResource::ATTRIBUTE_SITE, ResourceSite::ANILIST->value);

        if ($resource instanceof ExternalResource) {
            $id = $resource->external_id;

            $response = Http::get("https://api.ani.zip/v1/mappings?anilist_id=$id")
                ->throw()
                ->json();

            $this->response = $response;
        }

        return $this;
    }

    /**
     * Get the mapped resources.
     *
     * @return string[]
     */
    public function getResources(): array
    {
        $resources = [];

        if ($links = $this->response) {
            foreach ($this->getResourcesMapping() as $site => $key) {
                $id = Arr::get($links, $key);

                if ($id !== null) {
                    $resources[$site] = ResourceSite::from($site)
                        ->formatResourceLink(
                            Anime::class,
                            is_numeric($id) ? (int) $id : null,
                            is_string($id) ? $id : null
                        );
                }
            }
        }

        return $resources;
    }

    /**
     * Get the available sites to backfill.
     */
    public function getResourcesMapping(): array
    {
        return [
            ResourceSite::ANIME_PLANET->value => 'animeplanet_id',
            ResourceSite::ANIDB->value => 'anidb_id',
            ResourceSite::ANILIST->value => 'anilist_id',
            ResourceSite::KITSU->value => 'kitsu_id',
            ResourceSite::LIVECHART->value => 'livechart_id',
            ResourceSite::MAL->value => 'mal_id',
        ];
    }
}
