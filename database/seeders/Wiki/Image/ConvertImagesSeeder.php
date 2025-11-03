<?php

declare(strict_types=1);

namespace Database\Seeders\Wiki\Image;

use App\Actions\Models\Wiki\Image\OptimizeImageAction;
use App\Enums\Models\Wiki\ImageFacet;
use App\Models\Wiki\Image;
use Exception;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Collection;

class ConvertImagesSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Run the database seeds.
     *
     * @throws Exception
     */
    public function run(): void
    {
        Image::query()
            ->where(Image::ATTRIBUTE_FACET, ImageFacet::SMALL_COVER->value)
            ->where(Image::ATTRIBUTE_PATH, 'not like', '%.avif')
            ->chunkById(100, fn (Collection $images) => $images->each(function (Image $image): void {
                $action = new OptimizeImageAction($image, 'avif');

                $action->handle();
            }));
    }
}
