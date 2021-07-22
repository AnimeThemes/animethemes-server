<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Enums\Models\Wiki\ResourceSite;
use App\Models\Wiki\Anime;
use App\Models\Wiki\ExternalResource;
use App\Pivots\AnimeResource;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Exception\ServerException;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Seeder;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Log;

/**
 * Class AniDbResourceSeeder.
 */
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
        $animes = Anime::query()
            ->whereHas('resources', function (Builder $resourceQuery) {
                $resourceQuery->where('site', ResourceSite::MAL);
            })->whereDoesntHave('resources', function (Builder $resourceQuery) {
                $resourceQuery->where('site', ResourceSite::ANIDB);
            })
            ->get();

        foreach ($animes as $anime) {
            if (! $anime instanceof Anime) {
                continue;
            }

            $malResource = $anime->resources()->firstWhere('site', ResourceSite::MAL);
            if ($malResource instanceof ExternalResource && $malResource->external_id !== null) {
                // Try not to upset Yuna
                sleep(rand(5, 15));

                // Yuna api call
                try {
                    $client = new Client();
                    $response = $client->get("https://relations.yuna.moe/api/ids?source=myanimelist&id={$malResource->external_id}");

                    $anidbResourceJson = json_decode($response->getBody()->getContents(), true);
                    $anidbId = Arr::get($anidbResourceJson, 'anidb');

                    // Only proceed if we have a match
                    if ($anidbId !== null) {
                        // Check if AniDB resource already exists
                        $anidbResource = ExternalResource::query()
                            ->where('site', ResourceSite::ANIDB)
                            ->where('external_id', $anidbId)
                            ->first();

                        // Create AniDB resource if it doesn't already exist
                        if ($anidbResource === null) {
                            Log::info("Creating anidb resource '{$anidbId}' for anime '{$anime->name}'");

                            $anidbResource = ExternalResource::factory()->createOne([
                                'site' => ResourceSite::ANIDB,
                                'link' => "https://anidb.net/anime/{$anidbId}",
                                'external_id' => $anidbId,
                            ]);
                        }

                        // Attach AniDB resource to anime
                        if (AnimeResource::query()
                            ->where($anime->getKeyName(), $anime->getKey())
                            ->where($anidbResource->getKeyName(), $anidbResource->getKey())
                            ->doesntExist()
                        ) {
                            Log::info("Attaching resource '{$anidbResource->link}' to anime '{$anime->name}'");
                            $anidbResource->anime()->attach($anime);
                        }
                    }
                } catch (ClientException $e) {
                    // We may not have a match for this MAL resource
                    Log::info($e->getMessage());
                } catch (ServerException | GuzzleException $e) {
                    // We may have upset Yuna, try again later
                    Log::info($e->getMessage());

                    return;
                }
            }
        }
    }
}
