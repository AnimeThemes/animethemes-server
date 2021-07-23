<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Enums\Models\Wiki\ResourceSite;
use App\Models\Wiki\Anime;
use App\Models\Wiki\ExternalResource;
use App\Pivots\AnimeResource;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Seeder;
use Illuminate\Http\Client\RequestException;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Http;
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
        $animes = Anime::query()
            ->whereHas('resources', function (Builder $resourceQuery) {
                $resourceQuery->where('site', ResourceSite::MAL);
            })->whereDoesntHave('resources', function (Builder $resourceQuery) {
                $resourceQuery->where('site', ResourceSite::ANILIST);
            })
            ->get();

        foreach ($animes as $anime) {
            if (! $anime instanceof Anime) {
                continue;
            }

            $malResource = $anime->resources()->firstWhere('site', ResourceSite::MAL);
            if ($malResource instanceof ExternalResource && $malResource->external_id !== null) {
                // Try not to upset Anilist
                sleep(rand(2, 5));

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
                    $response = Http::post('https://graphql.anilist.co', [
                        'query' => $query,
                        'variables' => $variables,
                    ])
                        ->throw()
                        ->json();

                    $anilistId = Arr::get($response, 'data.Media.id');

                    // Check if Anilist resource already exists
                    $anilistResource = ExternalResource::query()
                        ->where('site', ResourceSite::ANILIST)
                        ->where('external_id', $anilistId)
                        ->first();

                    // Create Anilist resource if it doesn't already exist
                    if ($anilistResource === null) {
                        Log::info("Creating anilist resource '{$anilistId}' for anime '{$anime->name}'");

                        $anilistResource = ExternalResource::factory()->createOne([
                            'site' => ResourceSite::ANILIST,
                            'link' => "https://anilist.co/anime/{$anilistId}/",
                            'external_id' => $anilistId,
                        ]);
                    }

                    // Attach Anilist resource to anime
                    if (
                        AnimeResource::query()
                            ->where($anime->getKeyName(), $anime->getKey())
                            ->where($anilistResource->getKeyName(), $anilistResource->getKey())
                            ->doesntExist()
                    ) {
                        Log::info("Attaching resource '{$anilistResource->link}' to anime '{$anime->name}'");
                        $anilistResource->anime()->attach($anime);
                    }
                } catch (RequestException $e) {
                    Log::info($e->getMessage());
                }
            }
        }
    }
}
