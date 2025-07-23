<?php

declare(strict_types=1);

namespace App\Concerns\Models;

use App\Contracts\Models\HasImages;
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

trait CanCreateImage
{
    use HasLabel;

    /**
     * Create Image from response.
     *
     * @throws RequestException
     */
    public function createImageFromUrl(string $url, ImageFacet $facet, (BaseModel&HasImages)|null $model = null): Image
    {
        $imageResponse = Http::get($url)->throw();

        $image = $imageResponse->body();

        $file = File::createWithContent(basename($url), $image);

        /** @var \Illuminate\Filesystem\FilesystemAdapter $fs */
        $fs = Storage::disk(Config::get('image.disk'));

        $fsFile = $fs->putFile($this->path($facet, $model), $file);

        Log::info("Creating Image {$fsFile}");
        /** @var Image $image */
        $image = Image::query()->create([
            Image::ATTRIBUTE_FACET => $facet->value,
            Image::ATTRIBUTE_PATH => $fsFile,
        ]);

        $this->attachImage($image, $model);

        return $image;
    }

    /**
     * Create the images.
     */
    public function createImageFromFile(mixed $image, ImageFacet $facet, (BaseModel&HasImages)|null $model = null): Image
    {
        /** @var \Illuminate\Filesystem\FilesystemAdapter $fs */
        $fs = Storage::disk(Config::get('image.disk'));

        $fsFile = $fs->putFile($this->path($facet, $model), $image);

        Log::info("Creating Image {$fsFile}");
        /** @var Image $image */
        $image = Image::query()->create([
            Image::ATTRIBUTE_FACET => $facet->value,
            Image::ATTRIBUTE_PATH => $fsFile,
        ]);

        $this->attachImage($image, $model);

        return $image;
    }

    /**
     * Path to storage image in filesystem.
     */
    protected function path(ImageFacet $facet, (BaseModel&HasImages)|null $model): string
    {
        $path = Str::of('');

        if ($model !== null) {
            $path = $path
                ->append(Str::kebab(class_basename($model)))
                ->append(DIRECTORY_SEPARATOR);
        }

        return $path
            ->append(Str::kebab($facet->localize()))
            ->__toString();
    }

    /**
     * Try attach the image.
     */
    protected function attachImage(Image $image, (BaseModel&HasImages)|null $model): void
    {
        if ($model !== null) {
            Log::info("Attaching Image {$image->getName()} to {$this->privateLabel($model)} {$model->getName()}");
            $model->images()->attach($image);
        }
    }
}
