<?php

namespace Database\Seeders;

use App\Enums\AnimeSeason;
use App\Enums\ResourceSite;
use App\Models\Anime;
use GuzzleHttp\Client;
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

                $client = new Client;
                $response = $client->get("https://api.myanimelist.net/v2/anime/{$mal_resource->external_id}?fields=start_season", [
                    'headers' => [
                        'Authorization' => 'Bearer '.Config::get('app.mal_bearer_token'),
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
            }
        }
    }
}
