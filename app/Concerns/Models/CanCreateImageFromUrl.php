<?php

declare(strict_types=1);

namespace App\Concerns\Models;

use App\Enums\Models\Wiki\ImageFacet;
use App\Models\BaseModel;
use App\Models\Wiki\Image;
use Illuminate\Http\Client\RequestException;
use Illuminate\Http\Testing\File;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

/**
 * Trait CanCreateImageFromUrl.
 */
trait CanCreateImageFromUrl
{
    /**
     * Create Image from response.
     *
     * @param  string  $url
     * @param  ImageFacet  $facet
     * @param  BaseModel  $model
     * @return Image
     *
     * @throws RequestException
     */
    public function createImage(string $url, ImageFacet $facet, BaseModel $model): Image
    {
        $imageResponse = Http::get($url)->throw();

        $image = $imageResponse->body();

        $file = File::createWithContent(basename($url), $image);

        $fs = Storage::disk(Config::get('image.disk'));

        $fsFile = $fs->putFile($this->path($facet, $model), $file);

        Log::info("Creating Image {$fsFile}");
        /** @var Image $image */
        $image = Image::query()->create([
            Image::ATTRIBUTE_FACET => $facet->value,
            Image::ATTRIBUTE_PATH => $fsFile,
        ]);

        return $image;
    }

    /**
     * Path to storage image in filesystem.
     *
     * @param  ImageFacet  $facet
     * @param  BaseModel  $model
     * @return string
     */
    protected function path(ImageFacet $facet, BaseModel $model): string
    {
        return Str::of(Str::kebab(class_basename($model)))
            ->append(DIRECTORY_SEPARATOR)
            ->append(Str::kebab($facet->localize()))
            ->__toString();
    }
}
