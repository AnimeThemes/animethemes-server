<?php

declare(strict_types=1);

namespace App\Actions\Models\Wiki\Anime\ExternalApi;

use App\Actions\Models\Wiki\ExternalApiAction;
use App\Contracts\Actions\Models\Wiki\BackfillImages;
use App\Contracts\Actions\Models\Wiki\BackfillResources;
use App\Contracts\Actions\Models\Wiki\BackfillSynonyms;
use App\Enums\Models\Wiki\AnimeSynonymType;
use App\Enums\Models\Wiki\ImageFacet;
use App\Enums\Models\Wiki\ResourceSite;
use App\Models\Wiki\Anime;
use App\Models\Wiki\ExternalResource;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Http;

/**
 * Class AnilistAnimeExternalApiAction.
 */
class AnilistAnimeExternalApiAction extends ExternalApiAction implements BackfillImages, BackfillResources, BackfillSynonyms
{
    /**
     * Get the site to backfill.
     *
     * @return ResourceSite
     */
    public function getSite(): ResourceSite
    {
        return ResourceSite::ANILIST;
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
            $query = '
            query ($id: Int) {
                Media (id: $id, type: ANIME) {
                    title {
                        romaji
                        english
                        native
                    }
                    coverImage {
                        extraLarge
                        medium
                    }
                    externalLinks {
                        url
                        site
                        language
                    }
                }
            }
            ';

            $variables = [
                'id' => $resource->external_id,
            ];

            $response = Http::post('https://graphql.anilist.co', [
                'query' => $query,
                'variables' => $variables,
            ])
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
            $links = Arr::get($response, 'data.Media.externalLinks');

            foreach ($links as $link) {
                $url = Arr::get($link, 'url');
                $siteAnilist = Arr::get($link, 'site');
                $language = Arr::get($link, 'language');

                foreach ($this->getResourcesMapping() as $site => $key) {
                    if ($siteAnilist === $key) {
                        if (in_array($siteAnilist, ['Official Site', 'Twitter']) && ! in_array($language, ['Japanese', null])) {
                            continue;
                        }

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
    public function getResourcesMapping(): array
    {
        return [
            ResourceSite::X->value => 'Twitter',
            ResourceSite::OFFICIAL_SITE->value => 'Official Site',
            ResourceSite::NETFLIX->value => 'Netflix',
            ResourceSite::CRUNCHYROLL->value => 'Crunchyroll',
            ResourceSite::HIDIVE->value => 'HIDIVE',
            ResourceSite::AMAZON_PRIME_VIDEO->value => 'Amazon Prime Video',
            ResourceSite::HULU->value => 'Hulu',
            ResourceSite::DISNEY_PLUS->value => 'Disney Plus',
        ];
    }

    /**
     * Get the mapping for the images.
     *
     * @return array<int, string>
     */
    public function getImagesMapping(): array
    {
        return [
            ImageFacet::SMALL_COVER->value => 'data.Media.coverImage.medium',
            ImageFacet::LARGE_COVER->value => 'data.Media.coverImage.extraLarge',
        ];
    }

    /**
     * Get the mapping for the synonyms.
     *
     * @return array<int, string>
     */
    public function getSynonymsMapping(): array
    {
        return [
            AnimeSynonymType::ENGLISH->value => 'data.Media.title.english',
            AnimeSynonymType::NATIVE->value => 'data.Media.title.native',
            AnimeSynonymType::OTHER->value => 'data.Media.title.romaji',
        ];
    }
}
