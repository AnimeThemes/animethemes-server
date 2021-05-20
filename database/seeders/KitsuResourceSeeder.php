<?php

namespace Database\Seeders;

use App\Enums\ResourceSite;
use App\Models\Anime;
use App\Models\ExternalResource;
use App\Pivots\AnimeResource;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Exception\ServerException;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpKernel\Exception\HttpException;

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
            $mal_resource = $anime->externalResources()->firstWhere('site', strval(ResourceSite::MAL));
            if (! is_null(optional($mal_resource)->external_id)) {

                // Try not to upset Kitsu
                sleep(rand(5, 15));

                // Kitsu api call
                try {
                    $client = new Client;
                    $response = $client->get('https://kitsu.io/api/edge/mappings?filter[externalSite]=myanimelist/anime&include=item&filter[externalId]='.$mal_resource->external_id, [
                        'headers' => [
                            'Accept' => 'application/vnd.api+json',
                            'Content-Type' => 'application/vnd.api+json',
                        ],
                    ]);
                    $kitsu_resource_json = json_decode($response->getBody()->getContents());

                    $kitsu_resource_data = property_exists($kitsu_resource_json, 'data') ? $kitsu_resource_json->data : [];
                    $kitsu_resource_included = property_exists($kitsu_resource_json, 'included') ? $kitsu_resource_json->included : [];

                    // Only proceed if we have a single match
                    if (count($kitsu_resource_data) == 1 && count($kitsu_resource_included) == 1) {
                        $kitsu_id = $kitsu_resource_included[0]->id;
                        $kitsu_slug = $kitsu_resource_included[0]->attributes->slug;

                        // Check if Kitsu resource already exists
                        $kitsu_resource = ExternalResource::where('site', ResourceSite::KITSU)->where('external_id', $kitsu_id)->first();

                        // Create Kitsu resource if it doesn't already exist
                        if (is_null($kitsu_resource)) {
                            Log::info("Creating kitsu resource '{$kitsu_id}' for anime '{$anime->name}'");
                            $kitsu_resource = ExternalResource::create([
                                'site' => ResourceSite::KITSU,
                                'link' => "https://kitsu.io/anime/{$kitsu_slug}",
                                'external_id' => $kitsu_id,
                            ]);
                        }

                        // Attach Kitsu resource to anime
                        if (AnimeResource::where($anime->getKeyName(), $anime->getKey())->where($kitsu_resource->getKeyName(), $kitsu_resource->getKey())->doesntExist()) {
                            Log::info("Attaching resource '{$kitsu_resource->link}' to anime '{$anime->name}'");
                            $kitsu_resource->anime()->attach($anime);
                        }
                    }
                } catch (HttpException $e) {
                    // There was some issue with the request
                    Log::info($e->getMessage());
                } catch (ClientException $e) {
                    // We may not have a match for this MAL resource
                    Log::info($e->getMessage());
                } catch (ServerException $e) {
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
     * @return \Illuminate\Database\Eloquent\Collection
     */
    private function getUnseededAnime()
    {
        return Anime::query()
            ->whereHas('externalResources', function ($resource_query) {
                $resource_query->where('site', ResourceSite::MAL);
            })->whereDoesntHave('externalResources', function ($resource_query) {
                $resource_query->where('site', ResourceSite::KITSU);
            })
            ->get();
    }
}
