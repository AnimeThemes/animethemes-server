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
            $malResource = $anime->externalResources()->firstWhere('site', strval(ResourceSite::MAL));
            if (! is_null(optional($malResource)->external_id)) {

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
                    'id' => $malResource->external_id,
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
                    $anilistResourceJson = json_decode($response->getBody()->getContents());
                    $anilistId = $anilistResourceJson->data->Media->id;

                    // Check if Anilist resource already exists
                    $anilistResource = ExternalResource::where('site', ResourceSite::ANILIST)->where('external_id', $anilistId)->first();

                    // Create Anilist resource if it doesn't already exist
                    if (is_null($anilistResource)) {
                        Log::info("Creating anilist resource '{$anilistId}' for anime '{$anime->name}'");
                        $anilistResource = ExternalResource::create([
                            'site' => ResourceSite::ANILIST,
                            'link' => "https://anilist.co/anime/{$anilistId}/",
                            'external_id' => $anilistId,
                        ]);
                    }

                    // Attach Anilist resource to anime
                    if (AnimeResource::where($anime->getKeyName(), $anime->getKey())->where($anilistResource->getKeyName(), $anilistResource->getKey())->doesntExist()) {
                        Log::info("Attaching resource '{$anilistResource->link}' to anime '{$anime->name}'");
                        $anilistResource->anime()->attach($anime);
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
    protected function getUnseededAnime()
    {
        return Anime::query()
            ->whereHas('externalResources', function ($resourceQuery) {
                $resourceQuery->where('site', ResourceSite::MAL);
            })->whereDoesntHave('externalResources', function ($resourceQuery) {
                $resourceQuery->where('site', ResourceSite::ANILIST);
            })
            ->get();
    }
}
