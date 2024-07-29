<?php

declare(strict_types=1);

namespace App\Concerns\Models;

use App\Enums\Models\Wiki\ImageFacet;
use App\Models\BaseModel;
use App\Models\Wiki\Image;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Http\Client\RequestException;
use Illuminate\Http\Testing\File;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

/**
 * Trait CanCreateImage.
 */
trait CanCreateImage
{
    /**
     * Create Image from response.
     *
     * @param  string  $url
     * @param  ImageFacet  $facet
     * @param  BaseModel|null  $model
     * @return Image
     *
     * @throws RequestException
     */
    public function createImageFromUrl(string $url, ImageFacet $facet, ?BaseModel $model = null): Image
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

        $this->attach($image, $model);

        return $image;
    }

    /**
     * Create the images.
     *
     * @param  mixed  $image
     * @param  ImageFacet  $facet
     * @param  BaseModel|null  $model
     * @return Image
     */
    public function createImageFromFile(mixed $image, ImageFacet $facet, ?BaseModel $model = null): Image
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

        $this->attach($image, $model);

        return $image;
    }

    /**
     * Path to storage image in filesystem.
     *
     * @param  ImageFacet  $facet
     * @param  BaseModel|null  $model
     * @return string
     */
    protected function path(ImageFacet $facet, ?BaseModel $model): string
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
     *
     * @param  Image  $image
     * @param  BaseModel|null  $model
     * @return void
     */
    protected function attach(Image $image, ?BaseModel $model): void
    {
        if ($model !== null) {
            $images = $model->images();

            if ($images instanceof BelongsToMany) {
                Log::info("Attaching Image {$image->getName()} to {$this->label($model)} {$model->getName()}");
                $images->attach($image);
            }
        }
    }

    /**
     * Get the human-friendly label for the underlying model.
     *
     * @param  BaseModel  $model
     * @return string
     */
    private function label(BaseModel $model): string
    {
        return Str::headline(class_basename($model));
    }
}
