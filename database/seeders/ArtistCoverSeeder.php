<?php

namespace Database\Seeders;

use App\Enums\ImageFacet;
use App\Enums\ResourceSite;
use App\Models\Artist;
use App\Models\Image;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Exception\ServerException;
use Illuminate\Database\Seeder;
use Illuminate\Http\Testing\File;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class ArtistCoverSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Get artists that have MAL resource but do not have Anilist resource
        $artists = Artist::whereHas('externalResources', function ($resource_query) {
            $resource_query->where('site', ResourceSite::ANILIST);
        })->whereDoesntHave('images', function ($image_query) {
            $image_query->whereIn('facet', [ImageFacet::COVER_LARGE, ImageFacet::COVER_SMALL]);
        })
        ->get();

        $fs = Storage::disk('images');

        foreach ($artists as $artist) {
            $anilist_resource = $artist->externalResources->firstWhere('site', strval(ResourceSite::ANILIST));
            if (! is_null(optional($anilist_resource)->external_id)) {
                $artist_cover_large = $artist->images->firstWhere('facet', strval(ImageFacet::COVER_LARGE));
                $artist_cover_small = $artist->images->firstWhere('facet', strval(ImageFacet::COVER_SMALL));

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
                    'id' => $anilist_resource->external_id,
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
                    $anilist_resource_json = json_decode($response->getBody()->getContents());
                    $anilist_cover_large = $anilist_resource_json->data->Staff->image->large;
                    $anilist_cover_small = $anilist_resource_json->data->Staff->image->medium;

                    // Create large cover image
                    if (! is_null($anilist_cover_large) && is_null($artist_cover_large)) {
                        $cover_image_response = $client->get($anilist_cover_large);
                        $cover_image = $cover_image_response->getBody()->getContents();
                        $cover_file = File::createWithContent(basename($anilist_cover_large), $cover_image);
                        $cover_large = $fs->putFile('', $cover_file);

                        $cover_large_image = Image::create([
                            'facet' => ImageFacet::COVER_LARGE,
                            'path' => $cover_large,
                            'size' => $cover_image_response->getHeader('Content-Length')[0],
                            'mimetype' => $cover_image_response->getHeader('Content-Type')[0],
                        ]);

                        // Attach large cover to artist
                        Log::info("Attaching image '{$cover_large_image->path}' to artist '{$artist->name}'");
                        $cover_large_image->artists()->attach($artist);
                    }

                    // Create small cover image
                    if (! is_null($anilist_cover_small) && is_null($artist_cover_small)) {
                        $cover_image_response = $client->get($anilist_cover_small);
                        $cover_image = $cover_image_response->getBody()->getContents();
                        $cover_file = File::createWithContent(basename($anilist_cover_small), $cover_image);
                        $cover_small = $fs->putFile('', $cover_file);

                        $cover_small_image = Image::create([
                            'facet' => ImageFacet::COVER_SMALL,
                            'path' => $cover_small,
                            'size' => $cover_image_response->getHeader('Content-Length')[0],
                            'mimetype' => $cover_image_response->getHeader('Content-Type')[0],
                        ]);

                        // Attach large cover to artist
                        Log::info("Attaching image '{$cover_small_image->path}' to artist '{$artist->name}'");
                        $cover_small_image->artists()->attach($artist);
                    }
                } catch (ClientException $e) {
                    // We may not have a match for this MAL resource
                    Log::info($e->getMessage());
                } catch (ServerException $e) {
                    // We may have upset Anilist
                    Log::info($e->getMessage());
                    abort(500);
                }
            }
        }
    }
}
