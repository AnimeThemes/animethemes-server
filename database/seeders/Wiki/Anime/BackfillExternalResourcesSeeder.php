<?php

declare(strict_types=1);

namespace Database\Seeders\Wiki\Anime;

use App\Enums\Models\Wiki\ResourceSite;
use App\Models\Wiki\Anime;
use App\Models\Wiki\ExternalResource;
use App\Pivots\Wiki\AnimeResource;
use Exception;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Seeder;
use Illuminate\Http\Client\RequestException;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;

class BackfillExternalResourcesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        try {
            DB::beginTransaction();

            $chunkSize = 5;
            $animes = Anime::query()->where(Anime::ATTRIBUTE_ID, '>', 0)->get();

            foreach ($animes->chunk($chunkSize) as $chunk) {
                foreach ($chunk as $anime) {
                    if ($anime instanceof Anime) {
                        $externalLinks = $this->getExternalLinksByAnilistResource($anime);

                        if ($externalLinks === null) {
                            echo '$externalLinks null';
                            echo "\n";
                            continue;
                        }

                        $availableSites = $this->getAvailableSites();

                        foreach ($externalLinks as $externalLink) {
                            $site = $externalLink['site'];
                            $language = $externalLink['language'];

                            if (!in_array($site, array_keys($availableSites))) continue;
                            if (in_array($site, ['Official Site', 'Twitter']) && !in_array($language, ['Japanese', null])) continue;

                            if ($this->relation($anime)->getQuery()->where(ExternalResource::ATTRIBUTE_SITE, $availableSites[$site]->value)->exists()) {
                                $nameLocalized = $availableSites[$site]->localize();
                                echo "{$anime->anime_id} -> {$anime->getName()}: {$nameLocalized} already exists in the model ";
                                echo "\n";
                                continue;
                            }

                            $resource = $this->getOrCreateResource($externalLink, $anime);

                            if ($resource !== null) {
                                $this->attachResource($resource, $anime);
                            }

                            DB::commit();
                        }
                    }
                }
                sleep(11);
            }
            
            echo 'done';

        } catch (Exception $e) {
            echo 'error ' . $e->getMessage();
            echo "\n";

            DB::rollBack();

            throw $e;
        }
    }

    protected function getExternalLinksByAnilistResource(Anime $anime): ?array
    {
        $anilistResource = $this->getAnilistResource($anime);

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

            try {
                echo "{$anime->anime_id} Request";
                echo "\n";
            
                $response = Http::post('https://graphql.anilist.co', [
                    'query' => $query,
                    'variables' => $variables,
                ])
                    ->throw()
                    ->json();

            } catch (RequestException $e) {
                if ($e->response->status() === 404) {
                    return null;
                } else {
                    throw $e;
                }
            }

            $externalLinks = Arr::get($response, 'data.Media.externalLinks');

            return $externalLinks;
        }

        return null;
    }

    protected function getAnilistResource(Anime $anime): ?ExternalResource
    {
        $anilistResource = $anime->resources()->firstWhere(ExternalResource::ATTRIBUTE_SITE, ResourceSite::ANILIST->value);
        if ($anilistResource instanceof ExternalResource) {
            return $anilistResource;
        }

        return null;
    }

    protected function attachResource(ExternalResource $resource, Anime $anime): void
    {
        if (AnimeResource::query()
            ->where($anime->getKeyName(), $anime->getKey())
            ->where($resource->getKeyName(), $resource->getKey())
            ->doesntExist()
        ) {
            echo "{$anime->anime_id} -> {$anime->getName()}: Attaching Resource '{$resource->getName()}'";
            echo "\n";
            $this->relation($anime)->attach($resource);
        }
    }

    protected function getOrCreateResource(mixed $externalLink, Anime $anime): ExternalResource
    {
        $availableSites = $this->getAvailableSites();
        /** @var ResourceSite $resourceSite */
        $resourceSite = $availableSites[$externalLink['site']];
        $url = $externalLink['url'];
        $urlPattern = $resourceSite->getUrlPattern();

        if (preg_match($urlPattern, $url, $matches)) {
            $url = $resourceSite->formatAnimeResourceLink(intval($matches[2]), $matches[2], $matches[1]);
        }

        $resource = ExternalResource::query()
            ->where(ExternalResource::ATTRIBUTE_SITE, $resourceSite->value)
            ->where(ExternalResource::ATTRIBUTE_LINK, $url)
            ->orWhere(ExternalResource::ATTRIBUTE_LINK, $url . "/")
            ->first();

        if ($resource === null) {
            $nameLocalized = $resourceSite->localize();
            echo "{$anime->anime_id} -> {$anime->getName()}: Creating {$nameLocalized} -> '{$url}'";
            echo "\n";

            $resource = ExternalResource::query()->create([
                ExternalResource::ATTRIBUTE_LINK => $url,
                ExternalResource::ATTRIBUTE_SITE => $resourceSite->value,
                ExternalResource::ATTRIBUTE_EXTERNAL_ID => $resourceSite->parseIdFromLink($url),
            ]);
        }

        return $resource;
    }

    protected function relation(Anime $anime): BelongsToMany
    {
        return $anime->resources();
    }

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
}
