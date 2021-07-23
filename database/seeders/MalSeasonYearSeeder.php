<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Enums\Models\Wiki\AnimeSeason;
use App\Enums\Models\Wiki\ResourceSite;
use App\Models\Wiki\Anime;
use App\Models\Wiki\ExternalResource;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Seeder;
use Illuminate\Http\Client\RequestException;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

/**
 * Class MalSeasonYearSeeder.
 */
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
        $malBearerToken = Config::get('services.mal.token');
        if ($malBearerToken === null) {
            Log::error('MAL_BEARER_TOKEN must be configured in your env file.');

            return;
        }

        try {
            Http::withToken($malBearerToken)
                ->get('https://api.myanimelist.net/v2/users/@me')
                ->throw();
        } catch (RequestException $e) {
            Log::info($e->getMessage());

            return;
        }

        // Get anime that have MAL resource but do not have the season attribute set
        $animes = Anime::query()
            ->whereNull('season')
            ->whereHas('resources', function (Builder $resourceQuery) {
                $resourceQuery->where('site', ResourceSite::MAL);
            })
            ->get();

        foreach ($animes as $anime) {
            if (! $anime instanceof Anime) {
                continue;
            }

            $malResource = $anime->resources()->firstWhere('site', ResourceSite::MAL);
            if ($malResource instanceof ExternalResource && $malResource->external_id !== null) {
                // Try not to upset MAL
                sleep(rand(2, 5));

                try {
                    $response = Http::withToken($malBearerToken)
                        ->get("https://api.myanimelist.net/v2/anime/{$malResource->external_id}", [
                            'fields' => 'start_season',
                        ])
                        ->throw()
                        ->json();

                    $season = Arr::get($response, 'start_season.season');
                    $year = Arr::get($response, 'start_season.year');

                    if (AnimeSeason::hasKey(Str::upper($season))) {
                        $season = AnimeSeason::getValue(Str::upper($season));
                        $anime->season = $season;
                        Log::info("Setting season '{$season}' for anime '{$anime->name}'");
                    }
                    if (is_int($year)) {
                        $anime->year = $year;
                        Log::info("Setting year '{$year}' for anime '{$anime->name}'");
                    }
                    if ($anime->isDirty()) {
                        $anime->save();
                    }
                } catch (RequestException $e) {
                    Log::info($e->getMessage());
                }
            }
        }
    }
}
