<?php

declare(strict_types=1);

namespace App\Actions\Models\Wiki\Anime;

use App\Actions\Models\Wiki\BackfillExternalLinksAnilistResourceAction;
use App\Enums\Models\Wiki\ResourceSite;
use App\Models\Wiki\Anime;
use App\Models\Wiki\ExternalResource;
use App\Pivots\Wiki\AnimeResource;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

/**
 * Class BackfillAnimeExternalLinksAnilistResourceAction.
 *
 * @extends BackfillExternalLinksAnilistResourceAction<Anime>
 */
class BackfillAnimeExternalLinksAnilistResourceAction extends BackfillExternalLinksAnilistResourceAction
{
    /**
     * Create a new action instance.
     *
     * @param  Anime  $anime
     */
    public function __construct(Anime $anime)
    {
        parent::__construct($anime);
    }

    /**
     * Get the model the action is handling.
     *
     * @return Anime
     */
    protected function getModel(): Anime
    {
        return $this->model;
    }

    /**
     * Get the relation to resources.
     *
     * @return BelongsToMany
     */
    protected function relation(): BelongsToMany
    {
        return $this->getModel()->resources();
    }

    /**
     * Get the available sites to backfill.
     * 
     * @return array
     */
    protected function getAvailableSites(): array
    {
        /**  Key name in Anilist API => @var ResourceSite */
        return [
            'Twitter' => ResourceSite::TWITTER,
            'Official Site' => ResourceSite::OFFICIAL_SITE,
            'Netflix' => ResourceSite::NETFLIX,
            'Crunchyroll' => ResourceSite::CRUNCHYROLL,
            'HIDIVE' => ResourceSite::HIDIVE,
            'Amazon Prime Video' => ResourceSite::AMAZON_PRIME_VIDEO,
            'Hulu' => ResourceSite::HULU,
            'Disney Plus' => ResourceSite::DISNEY_PLUS,
        ];
    }

    /**
     * Get or Create Resource from response.
     *
     * @param  mixed  $externalLink
     * @return ExternalResource
     */
    protected function getOrCreateResource(mixed $externalLink): ExternalResource
    {
        $availableSites = $this->getAvailableSites();
        /** @var ResourceSite $resourceSite */
        $resourceSite = $availableSites[$externalLink['site']];
        $url = $externalLink['url'];
        $urlPattern = $resourceSite->getUrlPattern($this->getModel());

        if (preg_match($urlPattern, $url, $matches)) {
            $url = $resourceSite->formatAnimeResourceLink(intval($matches[2]), $matches[2], $matches[1]);
        }

        $resource = ExternalResource::query()
            ->where(ExternalResource::ATTRIBUTE_SITE, $resourceSite->value)
            ->where(ExternalResource::ATTRIBUTE_LINK, $url)
            ->orWhere(ExternalResource::ATTRIBUTE_LINK, $url . '/')
            ->first();

        if ($resource === null) {
            $nameLocalized = $resourceSite->localize();
            Log::info("Creating {$nameLocalized} -> '{$url}'");

            $resource = ExternalResource::query()->create([
                ExternalResource::ATTRIBUTE_LINK => $url,
                ExternalResource::ATTRIBUTE_SITE => $resourceSite->value,
                ExternalResource::ATTRIBUTE_EXTERNAL_ID => $resourceSite->parseIdFromLink($url),
            ]);
        }

        return $resource;
    }

    /**
     * Attach Resource to Anime.
     *
     * @param  ExternalResource  $resource
     * @return void
     */
    protected function attachResource(ExternalResource $resource): void
    {
        if (AnimeResource::query()
            ->where($this->getModel()->getKeyName(), $this->getModel()->getKey())
            ->where($resource->getKeyName(), $resource->getKey())
            ->doesntExist()
        ) {
            Log::info("Attaching Resource '{$resource->getName()}' to {$this->label()} '{$this->getModel()->getName()}'");
            $this->relation()->attach($resource);
        }
    }

    /**
     * Get the AniList Resource.
     *
     * @return ExternalResource|null
     */
    protected function getAnilistResource(): ?ExternalResource
    {
        $anilistResource = $this->getModel()->resources()->firstWhere(ExternalResource::ATTRIBUTE_SITE, ResourceSite::ANILIST->value);
        if ($anilistResource instanceof ExternalResource) {
            return $anilistResource;
        }

        return null;
    }

    /**
     * Get the external links by AniList API.
     * 
     * @return array|null
     */
    protected function getExternalLinksByAnilistResource(): ?array
    {
        $anilistResource = $this->getAnilistResource();

        if ($anilistResource !== null) {
            $query = '
            query ($id: Int) {
                Media (id: $id, type: ANIME) {
                    externalLinks {
                        url
                        site
                        language
                    }
                }
            }
            ';
    
            $variables = [
                'id' => $anilistResource->external_id
            ];
    
            $response = Http::post('https://graphql.anilist.co', [
                'query' => $query,
                'variables' => $variables,
            ])
                ->throw()
                ->json();
    
            $externalLinks = Arr::get($response, 'data.Media.externalLinks');
    
            return $externalLinks;
        }

        return null;
    }
}
