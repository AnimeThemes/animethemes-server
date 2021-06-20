<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Enums\Models\Wiki\ResourceSite;
use App\Models\Wiki\Anime;
use App\Models\Wiki\ExternalResource;
use App\Pivots\AnimeResource;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Exception\ServerException;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Seeder;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpKernel\Exception\HttpException;

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
        $animes = $this->getUnseededAnime();

        foreach ($animes as $anime) {
            $malResource = $anime->resources()->firstWhere('site', ResourceSite::MAL);
            if ($malResource !== null && $malResource->external_id !== null) {

                // Try not to upset Kitsu
                sleep(rand(5, 15));

                // Kitsu api call
                try {
                    $client = new Client();
                    $response = $client->get('https://kitsu.io/api/edge/mappings?filter[externalSite]=myanimelist/anime&include=item&filter[externalId]='.$malResource->external_id, [
                        'headers' => [
                            'Accept' => 'application/vnd.api+json',
                            'Content-Type' => 'application/vnd.api+json',
                        ],
                    ]);
                    $kitsuResourceJson = json_decode($response->getBody()->getContents(), true);

                    $kitsuResourceData = Arr::get($kitsuResourceJson, 'data', []);
                    $kitsuResourceIncluded = Arr::get($kitsuResourceJson, 'included', []);

                    // Only proceed if we have a single match
                    if (count($kitsuResourceData) == 1 && count($kitsuResourceIncluded) == 1) {
                        $kitsuId = $kitsuResourceIncluded[0]['id'];
                        $kitsuSlug = $kitsuResourceIncluded[0]['attributes']['slug'];

                        // Check if Kitsu resource already exists
                        $kitsuResource = ExternalResource::where('site', ResourceSite::KITSU)->where('external_id', $kitsuId)->first();

                        // Create Kitsu resource if it doesn't already exist
                        if ($kitsuResource === null) {
                            Log::info("Creating kitsu resource '{$kitsuId}' for anime '{$anime->name}'");
                            $kitsuResource = ExternalResource::create([
                                'site' => ResourceSite::KITSU,
                                'link' => "https://kitsu.io/anime/{$kitsuSlug}",
                                'external_id' => $kitsuId,
                            ]);
                        }

                        // Attach Kitsu resource to anime
                        if (AnimeResource::where($anime->getKeyName(), $anime->getKey())->where($kitsuResource->getKeyName(), $kitsuResource->getKey())->doesntExist()) {
                            Log::info("Attaching resource '{$kitsuResource->link}' to anime '{$anime->name}'");
                            $kitsuResource->anime()->attach($anime);
                        }
                    }
                } catch (HttpException $e) {
                    // There was some issue with the request
                    Log::info($e->getMessage());
                } catch (ClientException $e) {
                    // We may not have a match for this MAL resource
                    Log::info($e->getMessage());
                } catch (ServerException | GuzzleException $e) {
                    // We may have upset Kitsu, try again later
                    Log::info($e->getMessage());

                    return;
                }
            }
        }
    }

    /**
     * Get anime that have MAL resource but do not have Kitsu resource.
     *
     * @return Collection
     */
    protected function getUnseededAnime(): Collection
    {
        return Anime::query()
            ->whereHas('resources', function (Builder $resourceQuery) {
                $resourceQuery->where('site', ResourceSite::MAL);
            })->whereDoesntHave('resources', function (Builder $resourceQuery) {
                $resourceQuery->where('site', ResourceSite::KITSU);
            })
            ->get();
    }
}
