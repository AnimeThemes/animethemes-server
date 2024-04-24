<?php

declare(strict_types=1);

namespace Database\Seeders\Wiki\Image;

use App\Enums\Models\Wiki\ImageFacet;
use App\Models\Wiki\Anime;
use App\Models\Wiki\Artist;
use App\Models\Wiki\Image;
use App\Models\Wiki\Studio;
use Exception;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Seeder;
use Illuminate\Filesystem\FilesystemAdapter;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class MoveImageToAppropriateFolderSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->grillFacetImageSeeder();
        //$this->animeImageSeeder();
        $this->artistImageSeeder();
        //$this->studioImageSeeder();
    }

    protected function grillFacetImageSeeder(): void
    {
        try {
            DB::beginTransaction();

            $chunkSize = 100;
            /** @var FilesystemAdapter $fs */
            $fs = Storage::disk(Config::get('image.disk'));
            $images = Image::query()->where(Image::ATTRIBUTE_FACET, ImageFacet::GRILL)->get();

            foreach ($images->chunk($chunkSize) as $chunk) {
                foreach ($chunk as $image) {
                    if ($image instanceof Image) {
                        $oldPath = $image->path;
                        $newPath = "grill/{$image->path}";

                        $fs->move($oldPath, $newPath);

                        $image->update([
                            Image::ATTRIBUTE_PATH => $newPath,
                        ]);

                        DB::commit();

                        echo $oldPath . ' moved to ' . $newPath . "\n";
                    }
                }
                sleep(5);
            }

            echo "grill facet images done\n";

        } catch (Exception $e) {
            echo 'error ' . $e->getMessage() . "\n";

            DB::rollBack();

            throw $e;
        }
    }

    protected function animeImageSeeder(): void
    {
        try {
            DB::beginTransaction();

            $chunkSize = 100;
            /** @var FilesystemAdapter $fs */
            $fs = Storage::disk(Config::get('image.disk'));
            $animes = Anime::query()->where(Anime::ATTRIBUTE_ID, '>', 0)->whereHas(Image::TABLE, function (Builder $query) {
                $query->where(Image::ATTRIBUTE_PATH, 'not like', '%/%');
            })->get();

            foreach ($animes->chunk($chunkSize) as $chunk) {
                foreach ($chunk as $anime) {
                    if ($anime instanceof Anime) {
                        $images = $anime->images()->get();

                        if (count($images) === 0) continue;

                        $largeCover = $images->where(Image::ATTRIBUTE_FACET, ImageFacet::COVER_LARGE)->first();
                        $smallCover = $images->where(Image::ATTRIBUTE_FACET, ImageFacet::COVER_SMALL)->first();

                        if (!empty($largeCover)) {
                            $largeCoverOldPath = $largeCover->path;
                            $largeCoverNewPath = "anime/large-cover/{$largeCover->path}";

                            $fs->move($largeCoverOldPath, $largeCoverNewPath);

                            $largeCover->update([
                                Image::ATTRIBUTE_PATH => $largeCoverNewPath,
                            ]);

                            echo $largeCoverOldPath . ' moved to ' . $largeCoverNewPath . "\n";
                        }

                        if (!empty($smallCover)) {
                            $smallCoverOldPath = $smallCover->path;
                            $smallCoverNewPath = "anime/small-cover/{$smallCover->path}";

                            $fs->move($smallCoverOldPath, $smallCoverNewPath);

                            $smallCover->update([
                                Image::ATTRIBUTE_PATH => $smallCoverNewPath,
                            ]);

                            echo $smallCoverOldPath . ' moved to ' . $smallCoverNewPath . "\n";
                        }

                        DB::commit();
                    }
                }
                sleep(5);
            }

            echo "anime images done\n";


        } catch (Exception $e) {
            echo 'error ' . $e->getMessage() . "\n";

            DB::rollBack();

            throw $e;
        }
    }

    protected function artistImageSeeder(): void
    {
        try {
            DB::beginTransaction();

            $chunkSize = 100;
            /** @var FilesystemAdapter $fs */
            $fs = Storage::disk(Config::get('image.disk'));
            $artists = Artist::query()->where(Artist::ATTRIBUTE_ID, '>', 0)->whereHas(Image::TABLE, function (Builder $query) {
                $query->where(Image::ATTRIBUTE_PATH, 'not like', '%/%');
            })->get();

            foreach ($artists->chunk($chunkSize) as $chunk) {
                foreach ($chunk as $artist) {
                    if ($artist instanceof Artist) {
                        $images = $artist->images()->get();

                        if (count($images) === 0) continue;

                        $largeCover = $images->where(Image::ATTRIBUTE_FACET, ImageFacet::COVER_LARGE)->first();
                        $smallCover = $images->where(Image::ATTRIBUTE_FACET, ImageFacet::COVER_SMALL)->first();

                        if (!empty($largeCover)) {
                            $largeCoverOldPath = $largeCover->path;
                            $largeCoverNewPath = "artist/large-cover/{$largeCover->path}";

                            $fs->move($largeCoverOldPath, $largeCoverNewPath);

                            $largeCover->update([
                                Image::ATTRIBUTE_PATH => $largeCoverNewPath,
                            ]);

                            echo $largeCoverOldPath . ' moved to ' . $largeCoverNewPath . "\n";
                        }

                        if (!empty($smallCover)) {
                            $smallCoverOldPath = $smallCover->path;
                            $smallCoverNewPath = "artist/small-cover/{$smallCover->path}";

                            $fs->move($smallCoverOldPath, $smallCoverNewPath);

                            $smallCover->update([
                                Image::ATTRIBUTE_PATH => $smallCoverNewPath,
                            ]);

                            echo $smallCoverOldPath . ' moved to ' . $smallCoverNewPath . "\n";
                        }

                        DB::commit();
                    }
                }
                sleep(5);
            }

            echo "artist images done\n";

        } catch (Exception $e) {
            echo 'error ' . $e->getMessage() . "\n";

            DB::rollBack();

            throw $e;
        }
    }

    protected function studioImageSeeder(): void
    {
        try {
            DB::beginTransaction();

            $chunkSize = 100;
            /** @var FilesystemAdapter $fs */
            $fs = Storage::disk(Config::get('image.disk'));
            $studios = Studio::query()->where(Studio::ATTRIBUTE_ID, '>', 0)->whereHas(Image::TABLE, function (Builder $query) {
                $query->where(Image::ATTRIBUTE_PATH, 'not like', '%/%');
            })->get();

            foreach ($studios->chunk($chunkSize) as $chunk) {
                foreach ($chunk as $studio) {
                    if ($studio instanceof Studio) {
                        $images = $studio->images()->get();

                        if (count($images) === 0) continue;

                        $largeCover = $images->where(Image::ATTRIBUTE_FACET, ImageFacet::COVER_LARGE)->first();

                        if (!empty($largeCover)) {
                            $oldPath = $largeCover->path;
                            $newPath = "studio/large-cover/{$largeCover->path}";

                            $fs->move($oldPath, $newPath);

                            $largeCover->update([
                                Image::ATTRIBUTE_PATH => $newPath,
                            ]);

                            DB::commit();

                            echo $oldPath . ' moved to ' . $newPath . "\n";
                        }
                    }
                }
                sleep(5);
            }

            echo "studio images done\n";

        } catch (Exception $e) {
            echo 'error ' . $e->getMessage() . "\n";

            DB::rollBack();

            throw $e;
        }
    }
}