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
        $malClientID = Config::get('services.mal.client');
        if ($malClientID === null) {
            Log::error('MAL_CLIENT_ID must be configured in your env file.');

            return;
        }

        // Get anime that have MAL resource but do not have the season attribute set
        $animes = Anime::query()
            ->select([Anime::ATTRIBUTE_ID, Anime::ATTRIBUTE_NAME, Anime::ATTRIBUTE_YEAR, Anime::ATTRIBUTE_SEASON])
            ->whereNull(Anime::ATTRIBUTE_SEASON)
            ->whereHas(Anime::RELATION_RESOURCES, function (Builder $resourceQuery) {
                $resourceQuery->where(ExternalResource::ATTRIBUTE_SITE, ResourceSite::MAL);
            })
            ->get();

        foreach ($animes as $anime) {
            if (! $anime instanceof Anime) {
                continue;
            }

            $malResource = $anime->resources()->firstWhere(ExternalResource::ATTRIBUTE_SITE, ResourceSite::MAL);
            if ($malResource instanceof ExternalResource && $malResource->external_id !== null) {
                // Try not to upset MAL
                sleep(rand(2, 5));

                try {
                    $response = Http::withHeaders(['X-MAL-CLIENT-ID' => $malClientID])
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
