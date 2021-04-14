<?php

namespace Database\Seeders;

use App\Enums\AnimeSeason;
use App\Enums\ResourceSite;
use App\Models\Anime;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Exception\ServerException;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class MalSeasonYearSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Do not proceed if we do not have authorization to the MAL API
        $mal_bearer_token = Config::get('services.mal.token');
        if ($mal_bearer_token === null) {
            Log::error('MAL_BEARER_TOKEN must be configured in your env file.');

            return;
        }

        try {
            $client = new Client;
            $response = $client->get("https://api.myanimelist.net/v2/users/@me", [
                'headers' => [
                    'Authorization' => 'Bearer '.$mal_bearer_token,
                ],
            ]);
        } catch (ClientException $e) {
            Log::info($e->getMessage());

            return;
        } catch (ServerException $e) {
            Log::info($e->getMessage());

            return;
        }

        $animes = Anime::whereNull('season')
            ->whereHas('externalResources', function ($resource_query) {
                $resource_query->where('site', ResourceSite::MAL);
            })
            ->get();

        foreach ($animes as $anime) {
            $mal_resource = $anime->externalResources->firstWhere('site', strval(ResourceSite::MAL));
            if (! is_null(optional($mal_resource)->external_id)) {

                // Try not to upset MAL
                sleep(rand(5, 15));

                try {
                    $client = new Client;
                    $response = $client->get("https://api.myanimelist.net/v2/anime/{$mal_resource->external_id}?fields=start_season", [
                        'headers' => [
                            'Authorization' => 'Bearer '.$mal_bearer_token,
                        ],
                    ]);
                    $mal_resource_json = json_decode($response->getBody()->getContents());

                    if (! is_null(optional($mal_resource_json)->start_season)) {
                        if (is_int($mal_resource_json->start_season->year)) {
                            $anime->year = $mal_resource_json->start_season->year;
                            Log::info("Setting year '{$mal_resource_json->start_season->year}' for anime '{$anime->name}'");
                        }
                        if (AnimeSeason::hasKey(Str::upper($mal_resource_json->start_season->season))) {
                            $season = AnimeSeason::getValue(Str::upper($mal_resource_json->start_season->season));
                            $anime->season = $season;
                            Log::info("Setting season '{$season}' for anime '{$anime->name}'");
                        }
                        if ($anime->isDirty()) {
                            $anime->save();
                        }
                    }
                } catch (ClientException $e) {
                    Log::info($e->getMessage());
                } catch (ServerException $e) {
                    // We may have upset MAL, try again later
                    Log::info($e->getMessage());

                    return;
                }
            }
        }
    }
}
