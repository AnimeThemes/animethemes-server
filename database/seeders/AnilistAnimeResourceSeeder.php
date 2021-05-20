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

class AnilistAnimeResourceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Get anime that have MAL resource but do not have Anilist resource
        $animes = $this->getUnseededAnime();

        foreach ($animes as $anime) {
            $mal_resource = $anime->externalResources()->firstWhere('site', strval(ResourceSite::MAL));
            if (! is_null(optional($mal_resource)->external_id)) {

                // Try not to upset Anilist
                sleep(rand(5, 15));

                // Anilist graphql query
                $query = '
                query ($id: Int) {
                    Media (idMal: $id, type: ANIME) {
                        id
                    }
                }
                ';

                // Anilist graphql variables
                $variables = [
                    'id' => $mal_resource->external_id,
                ];

                // Anilist graphql api call
                try {
                    $client = new Client;
                    $response = $client->post('https://graphql.anilist.co', [
                        'json' => [
                            'query' => $query,
                            'variables' => $variables,
                        ],
                    ]);
                    $anilist_resource_json = json_decode($response->getBody()->getContents());
                    $anilist_id = $anilist_resource_json->data->Media->id;

                    // Check if Anilist resource already exists
                    $anilist_resource = ExternalResource::where('site', ResourceSite::ANILIST)->where('external_id', $anilist_id)->first();

                    // Create Anilist resource if it doesn't already exist
                    if (is_null($anilist_resource)) {
                        Log::info("Creating anilist resource '{$anilist_id}' for anime '{$anime->name}'");
                        $anilist_resource = ExternalResource::create([
                            'site' => ResourceSite::ANILIST,
                            'link' => "https://anilist.co/anime/{$anilist_id}/",
                            'external_id' => $anilist_id,
                        ]);
                    }

                    // Attach Anilist resource to anime
                    if (AnimeResource::where($anime->getKeyName(), $anime->getKey())->where($anilist_resource->getKeyName(), $anilist_resource->getKey())->doesntExist()) {
                        Log::info("Attaching resource '{$anilist_resource->link}' to anime '{$anime->name}'");
                        $anilist_resource->anime()->attach($anime);
                    }
                } catch (ClientException $e) {
                    // We may not have a match for this MAL resource
                    Log::info($e->getMessage());
                } catch (ServerException $e) {
                    // We may have upset Anilist, try again later
                    Log::info($e->getMessage());

                    return;
                }
            }
        }
    }

    /**
     * Get anime that have MAL resource but do not have Anilist resource.
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    private function getUnseededAnime()
    {
        return Anime::query()
            ->whereHas('externalResources', function ($resource_query) {
                $resource_query->where('site', ResourceSite::MAL);
            })->whereDoesntHave('externalResources', function ($resource_query) {
                $resource_query->where('site', ResourceSite::ANILIST);
            })
            ->get();
    }
}
