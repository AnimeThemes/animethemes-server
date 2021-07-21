<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Enums\Models\Wiki\ImageFacet;
use App\Enums\Models\Wiki\ResourceSite;
use App\Models\Wiki\Artist;
use App\Models\Wiki\ExternalResource;
use App\Models\Wiki\Image;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Exception\ServerException;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Seeder;
use Illuminate\Http\Testing\File;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

/**
 * Class ArtistCoverSeeder.
 */
class ArtistCoverSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Get artists that have MAL resource but not both cover images
        $artists = Artist::query()
            ->whereHas('resources', function (Builder $resourceQuery) {
                $resourceQuery->where('site', ResourceSite::ANILIST);
            })->whereDoesntHave('images', function (Builder $imageQuery) {
                $imageQuery->whereIn('facet', [ImageFacet::COVER_LARGE, ImageFacet::COVER_SMALL]);
            })
            ->get();

        $fs = Storage::disk('images');

        foreach ($artists as $artist) {
            if (! $artist instanceof Artist) {
                continue;
            }

            $anilistResource = $artist->resources()->firstWhere('site', ResourceSite::ANILIST);
            if ($anilistResource instanceof ExternalResource && $anilistResource->external_id !== null) {
                $artistCoverLarge = $artist->images()->where('facet', ImageFacet::COVER_LARGE)->first();
                $artistCoverSmall = $artist->images()->where('facet', ImageFacet::COVER_SMALL)->first();

                // Try not to upset Anilist
                sleep(rand(5, 15));

                // Anilist graphql query
                $query = '
                query ($id: Int) {
                    Staff (id: $id) {
                        image {
                            large
                            medium
                        }
                    }
                }
                ';

                // Anilist graphql variables
                $variables = [
                    'id' => $anilistResource->external_id,
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
                    $anilistCoverLarge = Arr::get($anilistResourceJson, 'data.Staff.image.large');
                    $anilistCoverSmall = Arr::get($anilistResourceJson, 'data.Staff.image.medium');

                    // Create large cover image
                    if ($anilistCoverLarge !== null && $artistCoverLarge === null) {
                        $coverImageResponse = $client->get($anilistCoverLarge);
                        $coverImage = $coverImageResponse->getBody()->getContents();
                        $coverFile = File::createWithContent(basename($anilistCoverLarge), $coverImage);
                        $coverLarge = $fs->putFile('', $coverFile);

                        $coverLargeImage = Image::factory()->createOne([
                            'facet' => ImageFacet::COVER_LARGE,
                            'path' => $coverLarge,
                            'size' => $coverImageResponse->getHeader('Content-Length')[0],
                            'mimetype' => $coverImageResponse->getHeader('Content-Type')[0],
                        ]);

                        // Attach large cover to artist
                        Log::info("Attaching image '{$coverLargeImage->path}' to artist '{$artist->name}'");
                        $coverLargeImage->artists()->attach($artist);
                    }

                    // Create small cover image
                    if ($anilistCoverSmall !== null && $artistCoverSmall === null) {
                        $coverImageResponse = $client->get($anilistCoverSmall);
                        $coverImage = $coverImageResponse->getBody()->getContents();
                        $coverFile = File::createWithContent(basename($anilistCoverSmall), $coverImage);
                        $coverSmall = $fs->putFile('', $coverFile);

                        $coverSmallImage = Image::factory()->createOne([
                            'facet' => ImageFacet::COVER_SMALL,
                            'path' => $coverSmall,
                            'size' => $coverImageResponse->getHeader('Content-Length')[0],
                            'mimetype' => $coverImageResponse->getHeader('Content-Type')[0],
                        ]);

                        // Attach large cover to artist
                        Log::info("Attaching image '{$coverSmallImage->path}' to artist '{$artist->name}'");
                        $coverSmallImage->artists()->attach($artist);
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
}
