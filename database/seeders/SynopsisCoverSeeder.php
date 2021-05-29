<?php

namespace Database\Seeders;

use App\Enums\ImageFacet;
use App\Enums\ResourceSite;
use App\Models\Anime;
use App\Models\Image;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Exception\ServerException;
use Illuminate\Database\Seeder;
use Illuminate\Http\Testing\File;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class SynopsisCoverSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Get anime that have MAL resource but not both cover images
        $animes = $this->getUnseededAnime();

        $fs = Storage::disk('images');

        foreach ($animes as $anime) {
            $anilistResource = $anime->externalResources()->firstWhere('site', strval(ResourceSite::ANILIST));
            if (! is_null(optional($anilistResource)->external_id)) {
                $animeCoverLarge = $anime->images()->firstWhere('facet', strval(ImageFacet::COVER_LARGE));
                $animeCoverSmall = $anime->images()->firstWhere('facet', strval(ImageFacet::COVER_SMALL));

                // Try not to upset Anilist
                sleep(rand(5, 15));

                // Anilist graphql query
                $query = '
                query ($id: Int) {
                    Media (id: $id, type: ANIME) {
                        description
                        coverImage {
                            extraLarge
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
                    $client = new Client;
                    $response = $client->post('https://graphql.anilist.co', [
                        'json' => [
                            'query' => $query,
                            'variables' => $variables,
                        ],
                    ]);
                    $anilistResourceJson = json_decode($response->getBody()->getContents());
                    $anilistSynopsis = $anilistResourceJson->data->Media->description;
                    $anilistCoverLarge = $anilistResourceJson->data->Media->coverImage->extraLarge;
                    $anilistCoverSmall = $anilistResourceJson->data->Media->coverImage->medium;

                    // Set Anime synopsis
                    if (! is_null($anilistSynopsis) && is_null($anime->synopsis)) {
                        Log::info("Setting synopsis for anime '{$anime->name}'");
                        $anime->synopsis = $anilistSynopsis;
                        $anime->save();
                    }

                    // Create large cover image
                    if (! is_null($anilistCoverLarge) && is_null($animeCoverLarge)) {
                        $coverImageResponse = $client->get($anilistCoverLarge);
                        $coverImage = $coverImageResponse->getBody()->getContents();
                        $coverFile = File::createWithContent(basename($anilistCoverLarge), $coverImage);
                        $coverLarge = $fs->putFile('', $coverFile);

                        $coverLargeImage = Image::create([
                            'facet' => ImageFacet::COVER_LARGE,
                            'path' => $coverLarge,
                            'size' => $coverImageResponse->getHeader('Content-Length')[0],
                            'mimetype' => $coverImageResponse->getHeader('Content-Type')[0],
                        ]);

                        // Attach large cover to anime
                        Log::info("Attaching image '{$coverLargeImage->path}' to anime '{$anime->name}'");
                        $coverLargeImage->anime()->attach($anime);
                    }

                    // Create small cover image
                    if (! is_null($anilistCoverSmall) && is_null($animeCoverSmall)) {
                        $coverImageResponse = $client->get($anilistCoverSmall);
                        $coverImage = $coverImageResponse->getBody()->getContents();
                        $coverFile = File::createWithContent(basename($anilistCoverSmall), $coverImage);
                        $coverSmall = $fs->putFile('', $coverFile);

                        $coverSmallImage = Image::create([
                            'facet' => ImageFacet::COVER_SMALL,
                            'path' => $coverSmall,
                            'size' => $coverImageResponse->getHeader('Content-Length')[0],
                            'mimetype' => $coverImageResponse->getHeader('Content-Type')[0],
                        ]);

                        // Attach large cover to anime
                        Log::info("Attaching image '{$coverSmallImage->path}' to anime '{$anime->name}'");
                        $coverSmallImage->anime()->attach($anime);
                    }
                } catch (ClientException $e) {
                    // We may not have a match for this MAL resource
                    Log::info($e->getMessage());
                } catch (ServerException $e) {
                    // We may have upset Anilist, try again later
                    Log::info($e->getMessage());

                    return;
                }
            }
        }
    }

    /**
     * Get anime that have MAL resource but not both cover images.
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    protected function getUnseededAnime()
    {
        return Anime::query()
            ->whereHas('externalResources', function ($resourceQuery) {
                $resourceQuery->where('site', ResourceSite::ANILIST);
            })->whereDoesntHave('images', function ($imageQuery) {
                $imageQuery->whereIn('facet', [ImageFacet::COVER_LARGE, ImageFacet::COVER_SMALL]);
            })
            ->get();
    }
}
