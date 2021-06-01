<?php declare(strict_types=1);

namespace Database\Seeders;

use App\Enums\AnimeSeason;
use App\Enums\ResourceSite;
use App\Models\Anime;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Exception\ServerException;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Seeder;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

/**
 * Class MalSeasonYearSeeder
 * @package Database\Seeders
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
            $client = new Client();
            $client->get('https://api.myanimelist.net/v2/users/@me', [
                'headers' => [
                    'Authorization' => 'Bearer '.$malBearerToken,
                ],
            ]);
        } catch (ClientException | ServerException | GuzzleException $e) {
            Log::info($e->getMessage());

            return;
        }

        // Get anime that have MAL resource but do not season
        $animes = $this->getUnseededAnime();

        foreach ($animes as $anime) {
            $malResource = $anime->externalResources()->firstWhere('site', ResourceSite::MAL);
            if ($malResource !== null && $malResource->external_id !== null) {

                // Try not to upset MAL
                sleep(rand(5, 15));

                try {
                    $client = new Client();
                    $response = $client->get("https://api.myanimelist.net/v2/anime/{$malResource->external_id}?fields=start_season", [
                        'headers' => [
                            'Authorization' => 'Bearer '.$malBearerToken,
                        ],
                    ]);
                    $malResourceJson = json_decode($response->getBody()->getContents(), true);

                    $season = Arr::get($malResourceJson, 'start_season.season');
                    $year = Arr::get($malResourceJson, 'start_season.year');

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
                } catch (ClientException $e) {
                    Log::info($e->getMessage());
                } catch (ServerException | GuzzleException $e) {
                    // We may have upset MAL, try again later
                    Log::info($e->getMessage());

                    return;
                }
            }
        }
    }

    /**
     * Get anime that have MAL resource but do not season.
     *
     * @return Collection
     */
    protected function getUnseededAnime(): Collection
    {
        return Anime::whereNull('season')
            ->whereHas('externalResources', function (BelongsToMany $resourceQuery) {
                $resourceQuery->where('site', ResourceSite::MAL);
            })
            ->get();
    }
}
