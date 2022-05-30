<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Enums\Models\Wiki\ImageFacet;
use App\Enums\Models\Wiki\ResourceSite;
use App\Models\Wiki\Anime;
use App\Models\Wiki\ExternalResource;
use App\Models\Wiki\Image;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Seeder;
use Illuminate\Http\Client\RequestException;
use Illuminate\Http\Testing\File;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

/**
 * Class SynopsisCoverSeeder.
 */
class SynopsisCoverSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run(): void
    {
        // Get anime that have MAL resource but not both cover images
        $animes = Anime::query()
            ->select([Anime::ATTRIBUTE_ID, Anime::ATTRIBUTE_NAME, Anime::ATTRIBUTE_SYNOPSIS])
            ->whereHas(Anime::RELATION_RESOURCES, function (Builder $resourceQuery) {
                $resourceQuery->where(ExternalResource::ATTRIBUTE_SITE, ResourceSite::ANILIST);
            })->whereDoesntHave(Anime::RELATION_IMAGES, function (Builder $imageQuery) {
                $imageQuery->whereIn(Image::ATTRIBUTE_FACET, [ImageFacet::COVER_LARGE, ImageFacet::COVER_SMALL]);
            })
            ->get();

        $fs = Storage::disk('images');

        foreach ($animes as $anime) {
            if (! $anime instanceof Anime) {
                continue;
            }

            $anilistResource = $anime->resources()->firstWhere(ExternalResource::ATTRIBUTE_SITE, ResourceSite::ANILIST);
            if ($anilistResource instanceof ExternalResource && $anilistResource->external_id !== null) {
                $animeCoverLarge = $anime->images()->where(Image::ATTRIBUTE_FACET, ImageFacet::COVER_LARGE)->first();
                $animeCoverSmall = $anime->images()->where(Image::ATTRIBUTE_FACET, ImageFacet::COVER_SMALL)->first();

                // Try not to upset Anilist
                sleep(rand(2, 5));

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
                    $response = Http::post('https://graphql.anilist.co', [
                        'query' => $query,
                        'variables' => $variables,
                    ])
                        ->throw()
                        ->json();

                    $anilistSynopsis = Arr::get($response, 'data.Media.description');
                    $anilistCoverLarge = Arr::get($response, 'data.Media.coverImage.extraLarge');
                    $anilistCoverSmall = Arr::get($response, 'data.Media.coverImage.medium');

                    // Set Anime synopsis
                    if ($anilistSynopsis !== null && $anime->synopsis === null) {
                        Log::info("Setting synopsis for anime '$anime->name'");
                        $anime->synopsis = $anilistSynopsis;
                        $anime->save();
                    }

                    // Create large cover image
                    if ($anilistCoverLarge !== null && $animeCoverLarge === null) {
                        $coverImageResponse = Http::get($anilistCoverLarge);
                        $coverImage = $coverImageResponse->body();
                        $coverFile = File::createWithContent(basename($anilistCoverLarge), $coverImage);
                        $coverLarge = $fs->putFile('', $coverFile);

                        $coverLargeImage = Image::factory()->createOne([
                            Image::ATTRIBUTE_FACET => ImageFacet::COVER_LARGE,
                            Image::ATTRIBUTE_PATH => $coverLarge,
                        ]);

                        // Attach large cover to anime
                        Log::info("Attaching image '$coverLargeImage->path' to anime '$anime->name'");
                        $coverLargeImage->anime()->attach($anime);
                    }

                    // Create small cover image
                    if ($anilistCoverSmall !== null && $animeCoverSmall === null) {
                        $coverImageResponse = Http::get($anilistCoverSmall);
                        $coverImage = $coverImageResponse->body();
                        $coverFile = File::createWithContent(basename($anilistCoverSmall), $coverImage);
                        $coverSmall = $fs->putFile('', $coverFile);

                        $coverSmallImage = Image::factory()->createOne([
                            Image::ATTRIBUTE_FACET => ImageFacet::COVER_SMALL,
                            Image::ATTRIBUTE_PATH => $coverSmall,
                        ]);

                        // Attach large cover to anime
                        Log::info("Attaching image '$coverSmallImage->path' to anime '$anime->name'");
                        $coverSmallImage->anime()->attach($anime);
                    }
                } catch (RequestException $e) {
                    Log::info($e->getMessage());
                }
            }
        }
    }
}
