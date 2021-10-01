<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Enums\Models\Wiki\ResourceSite;
use App\Models\Wiki\Anime;
use App\Models\Wiki\ExternalResource;
use App\Pivots\AnimeResource;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Seeder;
use Illuminate\Http\Client\RequestException;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

/**
 * Class KitsuResourceSeeder.
 */
class KitsuResourceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Get anime that have MAL resource but do not have Kitsu resource
        $animes = Anime::query()
            ->select([Anime::ATTRIBUTE_ID, Anime::ATTRIBUTE_NAME])
            ->whereHas(Anime::RELATION_RESOURCES, function (Builder $resourceQuery) {
                $resourceQuery->where(ExternalResource::ATTRIBUTE_SITE, ResourceSite::MAL);
            })->whereDoesntHave(Anime::RELATION_RESOURCES, function (Builder $resourceQuery) {
                $resourceQuery->where(ExternalResource::ATTRIBUTE_SITE, ResourceSite::KITSU);
            })
            ->get();

        foreach ($animes as $anime) {
            if (! $anime instanceof Anime) {
                continue;
            }

            $malResource = $anime->resources()->firstWhere(ExternalResource::ATTRIBUTE_SITE, ResourceSite::MAL);
            if ($malResource instanceof ExternalResource && $malResource->external_id !== null) {
                // Try not to upset Kitsu
                sleep(rand(2, 5));

                // Kitsu api call
                try {
                    $response = Http::contentType('application/vnd.api+json')
                        ->accept('application/vnd.api+json')
                        ->get('https://kitsu.io/api/edge/mappings', [
                            'include' => 'item',
                            'filter' => [
                                'externalSite' => 'myanimelist/anime',
                                'externalId' => $malResource->external_id,
                            ],
                        ])
                        ->throw()
                        ->json();

                    $kitsuResourceData = Arr::get($response, 'data', []);
                    $kitsuResourceIncluded = Arr::get($response, 'included', []);

                    // Only proceed if we have a single match
                    if (count($kitsuResourceData) === 1 && count($kitsuResourceIncluded) === 1) {
                        $kitsuId = $kitsuResourceIncluded[0]['id'];
                        $kitsuSlug = $kitsuResourceIncluded[0]['attributes']['slug'];

                        // Check if Kitsu resource already exists
                        $kitsuResource = ExternalResource::query()
                            ->select([ExternalResource::ATTRIBUTE_ID, ExternalResource::ATTRIBUTE_LINK])
                            ->where(ExternalResource::ATTRIBUTE_SITE, ResourceSite::KITSU)
                            ->where(ExternalResource::ATTRIBUTE_EXTERNAL_ID, $kitsuId)
                            ->first();

                        // Create Kitsu resource if it doesn't already exist
                        if ($kitsuResource === null) {
                            Log::info("Creating kitsu resource '{$kitsuId}' for anime '{$anime->name}'");

                            $kitsuResource = ExternalResource::factory()->createOne([
                                ExternalResource::ATTRIBUTE_EXTERNAL_ID => $kitsuId,
                                ExternalResource::ATTRIBUTE_LINK => "https://kitsu.io/anime/{$kitsuSlug}",
                                ExternalResource::ATTRIBUTE_SITE => ResourceSite::KITSU,
                            ]);
                        }

                        // Attach Kitsu resource to anime
                        if (AnimeResource::query()
                            ->where($anime->getKeyName(), $anime->getKey())
                            ->where($kitsuResource->getKeyName(), $kitsuResource->getKey())
                            ->doesntExist()
                        ) {
                            Log::info("Attaching resource '{$kitsuResource->link}' to anime '{$anime->name}'");
                            $kitsuResource->anime()->attach($anime);
                        }
                    }
                } catch (RequestException $e) {
                    Log::info($e->getMessage());
                }
            }
        }
    }
}
