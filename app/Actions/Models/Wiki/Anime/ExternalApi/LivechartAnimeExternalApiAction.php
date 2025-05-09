<?php

declare(strict_types=1);

namespace App\Actions\Models\Wiki\Anime\ExternalApi;

use App\Actions\Models\Wiki\ExternalApiAction;
use App\Contracts\Actions\Models\Wiki\BackfillResources;
use App\Enums\Models\Wiki\ResourceSite;
use App\Models\Wiki\Anime;
use App\Models\Wiki\ExternalResource;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Facades\Http;

/**
 * Class LivechartAnimeExternalApiAction.
 */
class LivechartAnimeExternalApiAction extends ExternalApiAction implements BackfillResources
{
    /**
     * Get the site to backfill.
     *
     * @return ResourceSite
     */
    public function getSite(): ResourceSite
    {
        return ResourceSite::LIVECHART;
    }

    /**
     * Set the response after the request.
     *
     * @param  BelongsToMany<ExternalResource, Anime>  $resources
     * @return static
     */
    public function handle(BelongsToMany $resources): static
    {
        $resource = $resources->firstWhere(ExternalResource::ATTRIBUTE_SITE, ResourceSite::LIVECHART->value);

        if ($resource instanceof ExternalResource) {
            $id = $resource->external_id;

            $response = Http::withUserAgent('AnimeThemes/1.0 (https://animethemes.moe)')
                ->get("https://www.livechart.me/api/v2/anime/$id")
                ->throw()
                ->json();

            $this->response = $response;
        }

        return $this;
    }

    /**
     * Get the mapping for the resources.
     *
     * @return array<int, string>
     */
    public function getResourcesMapping(): array
    {
        return [
            ResourceSite::ANIDB->value => 'anidb_url',
            ResourceSite::ANILIST->value => 'anilist_url',
            ResourceSite::ANIME_PLANET->value => 'anime_planet_url',
            ResourceSite::ANN->value => 'ann_url',
            ResourceSite::KITSU->value => 'kitsu_url',
            ResourceSite::MAL->value => 'mal_url',
            ResourceSite::OFFICIAL_SITE->value => 'website_url',
            ResourceSite::X->value => 'twitter_url',
        ];
    }
}
