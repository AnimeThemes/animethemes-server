<?php

namespace Database\Seeders;

use App\Enums\ResourceSite;
use App\Models\Artist;
use App\Models\ExternalResource;
use App\Pivots\ArtistResource;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Exception\ServerException;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Log;

class AnilistArtistResourceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Get artists that have MAL resource but do not have Anilist resource
        $artists = Artist::whereDoesntHave('externalResources', function ($resourceQuery) {
            $resourceQuery->where('site', ResourceSite::ANILIST);
        })
        ->get();

        foreach ($artists as $artist) {
            // Try not to upset Anilist
            sleep(rand(5, 15));

            // Anilist graphql query
            $query = '
            query ($artistQuery: String) {
                Staff (search: $artistQuery) {
                    id
                }
            }
            ';

            // Anilist graphql variables
            $variables = [
                'artistQuery' => $artist->name,
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
                $anilistId = $anilistResourceJson->data->Staff->id;

                // Check if Anilist resource already exists
                $anilistResource = ExternalResource::where('site', ResourceSite::ANILIST)->where('external_id', $anilistId)->first();

                // Create Anilist resource if it doesn't already exist
                if (is_null($anilistResource)) {
                    Log::info("Creating anilist resource '{$anilistId}' for artist '{$artist->name}'");
                    $anilistResource = ExternalResource::create([
                        'site' => ResourceSite::ANILIST,
                        'link' => "https://anilist.co/staff/{$anilistId}/",
                        'external_id' => $anilistId,
                    ]);
                }

                // Attach Anilist resource to artist
                if (ArtistResource::where($artist->getKeyName(), $artist->getKey())->where($anilistResource->getKeyName(), $anilistResource->getKey())->doesntExist()) {
                    Log::info("Attaching resource '{$anilistResource->link}' to artist '{$artist->name}'");
                    $anilistResource->artists()->attach($artist);
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
