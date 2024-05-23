<?php

declare(strict_types=1);

namespace App\Actions\Models\Wiki;

use App\Enums\Models\Wiki\ImageFacet;
use App\Models\Wiki\Image;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

/**
 * Class UploadImageAction.
 */
class UploadImageAction
{
    /**
     * Create the images.
     *
     * @param  array  $fields
     * @return void
     */
    public function handle(array $fields): void
    {
        $facet = ImageFacet::from(intval(Arr::get($fields, Image::ATTRIBUTE_FACET)));
        $image = Arr::get($fields, Image::ATTRIBUTE_PATH);

        /** @var \Illuminate\Filesystem\FilesystemAdapter */
        $fs = Storage::disk(Config::get('image.disk'));

        $fsFile = $fs->putFile($this->path($facet), $image);

        Image::query()->create([
            Image::ATTRIBUTE_FACET => $facet->value,
            Image::ATTRIBUTE_PATH => $fsFile,
        ]);
    }

    /**
     * Path to storage image in filesystem.
     *
     * @param  ImageFacet  $facet
     * @return string
     */
    protected function path(ImageFacet $facet): string
    {
        return Str::of(Str::kebab($facet->localize()))
            ->__toString();
    }
}
