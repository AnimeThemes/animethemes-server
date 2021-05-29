<?php

namespace Database\Seeders;

use App\Enums\ResourceSite;
use App\Models\Anime;
use App\Models\ExternalResource;
use App\Pivots\AnimeResource;
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
        $animes = $this->getUnseededAnime();

        foreach ($animes as $anime) {
            $malResource = $anime->externalResources()->firstWhere('site', strval(ResourceSite::MAL));
            if (! is_null(optional($malResource)->external_id)) {

                // Try not to upset Yuna
                sleep(rand(5, 15));

                // Yuna api call
                try {
                    $client = new Client;
                    $response = $client->get('https://relations.yuna.moe/api/ids?source=myanimelist&id='.$malResource->external_id);

                    $anidbResourceJson = json_decode($response->getBody()->getContents());
                    $anidbId = $anidbResourceJson !== null && property_exists($anidbResourceJson, 'anidb') ? $anidbResourceJson->anidb : null;

                    // Only proceed if we have a match
                    if ($anidbId !== null) {

                        // Check if AniDB resource already exists
                        $anidbResource = ExternalResource::where('site', ResourceSite::ANIDB)->where('external_id', $anidbId)->first();

                        // Create AniDB resource if it doesn't already exist
                        if (is_null($anidbResource)) {
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
                } catch (ServerException $e) {
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
     * @return \Illuminate\Database\Eloquent\Collection
     */
    protected function getUnseededAnime()
    {
        return Anime::query()
            ->whereHas('externalResources', function ($resourceQuery) {
                $resourceQuery->where('site', ResourceSite::MAL);
            })->whereDoesntHave('externalResources', function ($resourceQuery) {
                $resourceQuery->where('site', ResourceSite::ANIDB);
            })
            ->get();
    }
}
