<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Enums\ResourceSite;
use App\Models\Anime;
use App\Models\ExternalResource;
use App\Pivots\AnimeResource;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Exception\ServerException;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
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
        $animes = $this->getUnseededAnime();

        foreach ($animes as $anime) {
            $malResource = $anime->externalResources()->firstWhere('site', ResourceSite::MAL);
            if ($malResource !== null && $malResource->external_id !== null) {

                // Try not to upset Yuna
                sleep(rand(5, 15));

                // Yuna api call
                try {
                    $client = new Client();
                    $response = $client->get('https://relations.yuna.moe/api/ids?source=myanimelist&id='.$malResource->external_id);

                    $anidbResourceJson = json_decode($response->getBody()->getContents(), true);
                    $anidbId = Arr::get($anidbResourceJson, 'anidb');

                    // Only proceed if we have a match
                    if ($anidbId !== null) {

                        // Check if AniDB resource already exists
                        $anidbResource = ExternalResource::where('site', ResourceSite::ANIDB)->where('external_id', $anidbId)->first();

                        // Create AniDB resource if it doesn't already exist
                        if ($anidbResource === null) {
                            Log::info("Creating anidb resource '{$anidbId}' for anime '{$anime->name}'");
                            $anidbResource = ExternalResource::create([
                                'site' => ResourceSite::ANIDB,
                                'link' => "https://anidb.net/anime/{$anidbId}",
                                'external_id' => $anidbId,
                            ]);
                        }

                        // Attach AniDB resource to anime
                        if (AnimeResource::where($anime->getKeyName(), $anime->getKey())->where($anidbResource->getKeyName(), $anidbResource->getKey())->doesntExist()) {
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

    /**
     * Get anime that have MAL resource but do not have AniDB resource.
     *
     * @return Collection
     */
    protected function getUnseededAnime(): Collection
    {
        return Anime::query()
            ->whereHas('externalResources', function (BelongsToMany $resourceQuery) {
                $resourceQuery->where('site', ResourceSite::MAL);
            })->whereDoesntHave('externalResources', function (BelongsToMany $resourceQuery) {
                $resourceQuery->where('site', ResourceSite::ANIDB);
            })
            ->get();
    }
}
