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
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Seeder;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Log;

/**
 * Class AnilistAnimeResourceSeeder.
 */
class AnilistAnimeResourceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Get anime that have MAL resource but do not have Anilist resource
        $animes = $this->getUnseededAnime();

        foreach ($animes as $anime) {
            $malResource = $anime->externalResources()->firstWhere('site', ResourceSite::MAL);
            if ($malResource !== null && $malResource->external_id !== null) {

                // Try not to upset Anilist
                sleep(rand(5, 15));

                // Anilist graphql query
                $query = '
                query ($id: Int) {
                    Media (idMal: $id, type: ANIME) {
                        id
                    }
                }
                ';

                // Anilist graphql variables
                $variables = [
                    'id' => $malResource->external_id,
                ];

                // Anilist graphql api call
                try {
                    $client = new Client();
                    $response = $client->post('https://graphql.anilist.co', [
                        'json' => [
                            'query' => $query,
                            'variables' => $variables,
                        ],
                    ]);
                    $anilistResourceJson = json_decode($response->getBody()->getContents(), true);
                    $anilistId = Arr::get($anilistResourceJson, 'data.Media.id');

                    // Check if Anilist resource already exists
                    $anilistResource = ExternalResource::where('site', ResourceSite::ANILIST)->where('external_id', $anilistId)->first();

                    // Create Anilist resource if it doesn't already exist
                    if ($anilistResource === null) {
                        Log::info("Creating anilist resource '{$anilistId}' for anime '{$anime->name}'");
                        $anilistResource = ExternalResource::create([
                            'site' => ResourceSite::ANILIST,
                            'link' => "https://anilist.co/anime/{$anilistId}/",
                            'external_id' => $anilistId,
                        ]);
                    }

                    // Attach Anilist resource to anime
                    if (AnimeResource::where($anime->getKeyName(), $anime->getKey())->where($anilistResource->getKeyName(), $anilistResource->getKey())->doesntExist()) {
                        Log::info("Attaching resource '{$anilistResource->link}' to anime '{$anime->name}'");
                        $anilistResource->anime()->attach($anime);
                    }
                } catch (ClientException $e) {
                    // We may not have a match for this MAL resource
                    Log::info($e->getMessage());
                } catch (ServerException | GuzzleException $e) {
                    // We may have upset Anilist, try again later
                    Log::info($e->getMessage());

                    return;
                }
            }
        }
    }

    /**
     * Get anime that have MAL resource but do not have Anilist resource.
     *
     * @return Collection
     */
    protected function getUnseededAnime(): Collection
    {
        return Anime::query()
            ->whereHas('externalResources', function (Builder $resourceQuery) {
                $resourceQuery->where('site', ResourceSite::MAL);
            })->whereDoesntHave('externalResources', function (Builder $resourceQuery) {
                $resourceQuery->where('site', ResourceSite::ANILIST);
            })
            ->get();
    }
}
