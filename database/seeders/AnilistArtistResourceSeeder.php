<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Enums\Models\Wiki\ResourceSite;
use App\Models\Wiki\Artist;
use App\Models\Wiki\ExternalResource;
use App\Pivots\ArtistResource;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Seeder;
use Illuminate\Http\Client\RequestException;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

/**
 * Class AnilistArtistResourceSeeder.
 */
class AnilistArtistResourceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Get artists that have MAL resource but do not have Anilist resource
        $artists = Artist::query()
            ->select(['artist_id', 'name'])
            ->whereDoesntHave('resources', function (Builder $resourceQuery) {
                $resourceQuery->where('site', ResourceSite::ANILIST);
            })
            ->get();

        foreach ($artists as $artist) {
            if (! $artist instanceof Artist) {
                continue;
            }

            // Try not to upset Anilist
            sleep(rand(2, 5));

            // Anilist graphql query
            $query = '
            query ($artistQuery: String) {
                Staff (search: $artistQuery) {
                    id
                }
            }
            ';

            // Anilist graphql variables
            $variables = [
                'artistQuery' => $artist->name,
            ];

            // Anilist graphql api call
            try {
                $response = Http::post('https://graphql.anilist.co', [
                    'query' => $query,
                    'variables' => $variables,
                ])
                    ->throw()
                    ->json();

                $anilistId = Arr::get($response, 'data.Staff.id');

                // Check if Anilist resource already exists
                $anilistResource = ExternalResource::query()
                    ->select(['resource_id', 'link'])
                    ->where('site', ResourceSite::ANILIST)
                    ->where('external_id', $anilistId)
                    ->first();

                // Create Anilist resource if it doesn't already exist
                if ($anilistResource === null) {
                    Log::info("Creating anilist resource '{$anilistId}' for artist '{$artist->name}'");

                    $anilistResource = ExternalResource::factory()->createOne([
                        'site' => ResourceSite::ANILIST,
                        'link' => "https://anilist.co/staff/{$anilistId}/",
                        'external_id' => $anilistId,
                    ]);
                }

                // Attach Anilist resource to artist
                if (
                    ArtistResource::query()
                        ->where($artist->getKeyName(), $artist->getKey())
                        ->where($anilistResource->getKeyName(), $anilistResource->getKey())
                        ->doesntExist()
                ) {
                    Log::info("Attaching resource '{$anilistResource->link}' to artist '{$artist->name}'");
                    $anilistResource->artists()->attach($artist);
                }
            } catch (RequestException $e) {
                Log::info($e->getMessage());

                return;
            }
        }
    }
}
