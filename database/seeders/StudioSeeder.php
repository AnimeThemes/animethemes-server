<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Enums\Models\Wiki\ResourceSite;
use App\Models\Wiki\Anime;
use App\Models\Wiki\ExternalResource;
use App\Models\Wiki\Studio;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Seeder;
use Illuminate\Http\Client\RequestException;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

/**
 * Class StudioSeeder.
 */
class StudioSeeder extends Seeder
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

        $animes = Anime::query()
            ->select(['anime_id', 'name'])
            ->whereDoesntHave('studios')
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
                            'fields' => 'studios',
                        ])
                        ->throw()
                        ->json();

                    $malStudios = Arr::get($response, 'studios', []);

                    foreach ($malStudios as $malStudio) {
                        $name = Arr::get($malStudio, 'name');
                        if (empty($name)) {
                            continue;
                        }

                        $studio = Studio::query()->firstWhere('name', $name);
                        if (! $studio instanceof Studio) {
                            Log::info("Creating studio '{$name}'");

                            $studio = Studio::factory()->createOne([
                                'name' => $name,
                                'slug' => Str::slug($name, '_'),
                            ]);
                        }

                        Log::info("Attaching studio '{$name}' to anime '{$anime->getName()}'");
                        $anime->studios()->attach($studio);
                    }
                } catch (RequestException $e) {
                    Log::info($e->getMessage());
                }
            }
        }
    }
}
