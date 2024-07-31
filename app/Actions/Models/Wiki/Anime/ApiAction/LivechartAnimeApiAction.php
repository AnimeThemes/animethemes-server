<?php

declare(strict_types=1);

namespace App\Actions\Models\Wiki\Anime\ApiAction;

use App\Actions\Models\Wiki\ApiAction;
use App\Enums\Models\Wiki\ImageFacet;
use App\Enums\Models\Wiki\ResourceSite;
use App\Models\Wiki\ExternalResource;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Facades\Http;

/**
 * Class LivechartAnimeApiAction.
 */
class LivechartAnimeApiAction extends ApiAction
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
     * @param  BelongsToMany<ExternalResource>  $resources
     * @return static
     */
    public function handle(BelongsToMany $resources): static
    {
        $resource = $resources->firstWhere(ExternalResource::ATTRIBUTE_SITE, ResourceSite::LIVECHART->value);

        if ($resource instanceof ExternalResource) {
            $id = $resource->external_id;

            $response = Http::withHeaders([
                'accept-encoding' => 'gzip',
                'connection' => 'Keep-Alive',
                'host' => 'www.livechart.me',
                'user-agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/123.0.0.0 Safari/537.36 OPR/109.0.0.0',
            ])->withoutVerifying()
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
    protected function getResourcesMapping(): array
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
