<?php

namespace Database\Seeders;

use App\Enums\ResourceSite;
use App\Models\Anime;
use App\Models\ExternalResource;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Exception\ServerException;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Log;

class AniDbResourceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Get anime that have MAL resource but do not have AniDB resource
        $animes = Anime::whereHas('externalResources', function ($resource_query) {
            $resource_query->where('site', ResourceSite::MAL);
        })->whereDoesntHave('externalResources', function ($resource_query) {
            $resource_query->where('site', ResourceSite::ANIDB);
        })
        ->get();

        foreach ($animes as $anime) {
            $mal_resource = $anime->externalResources->firstWhere('site', strval(ResourceSite::MAL));
            if (! is_null(optional($mal_resource)->external_id)) {

                // Try not to upset Yuna
                sleep(rand(5, 15));

                // Yuna api call
                try {
                    $client = new Client;
                    $response = $client->get('https://relations.yuna.moe/api/ids?source=myanimelist&id='.$mal_resource->external_id);

                    $anidb_resource_json = json_decode($response->getBody()->getContents());
                    $anidb_id = $anidb_resource_json != null && property_exists($anidb_resource_json, 'anidb') ? $anidb_resource_json->anidb : null;

                    // Only proceed if we have a match
                    if ($anidb_id != null) {

                        // Check if AniDB resource already exists
                        $anidb_resource = ExternalResource::where('site', ResourceSite::ANIDB)->where('external_id', $anidb_id)->first();

                        // Create AniDB resource if it doesn't already exist
                        if (is_null($anidb_resource)) {
                            $anidb_resource = ExternalResource::create([
                                'site' => ResourceSite::ANIDB,
                                'link' => "https://anidb.net/anime/{$anidb_id}",
                                'external_id' => $anidb_id,
                            ]);
                        }

                        // Attach AniDB resource to anime
                        $anidb_resource->anime()->syncWithoutDetaching($anime);
                    }
                } catch (ClientException $e) {
                    // We may not have a match for this MAL resource
                    Log::info($e->getMessage());
                } catch (ServerException $e) {
                    // We may have upset Yuna
                    Log::info($e->getMessage());
                    abort(500);
                }
            }
        }
    }
}
