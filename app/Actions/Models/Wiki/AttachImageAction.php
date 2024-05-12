<?php

declare(strict_types=1);

namespace App\Actions\Models\Wiki;

use App\Enums\Models\Wiki\ImageFacet;
use App\Models\BaseModel;
use App\Models\Wiki\Image;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

/**
 * Class AttachImageAction.
 */
abstract class AttachImageAction
{
    /**
     * Create a new action instance.
     *
     * @param  ImageFacet[]  $facets
     */
    public function __construct(protected array $facets)
    {
    }

    /**
     * Perform the action on the given models.
     *
     * @param  BaseModel  $model
     * @param  array  $fields
     * @return void
     */
    public function handle(BaseModel $model, array $fields): void
    {
        $images = $this->createImages($fields, $model);

        foreach ($images as $image) {
            $relation = $this->relation($image);

            $relation->attach($model);
        }
    }

    /**
     * Create the images.
     *
     * @param  array  $fields
     * @param  BaseModel  $model
     * @return Image[]
     */
    protected function createImages(array $fields, BaseModel $model): array
    {
        $images = [];

        foreach ($this->facets as $facet) {
            $image = Arr::get($fields, $facet->name);

            if (empty($image)) continue;

            /** @var \Illuminate\Filesystem\FilesystemAdapter */
            $fs = Storage::disk(Config::get('image.disk'));

            $fsFile = $fs->putFile($this->path($facet, $model), $image);

            $image = Image::query()->create([
                Image::ATTRIBUTE_FACET => $facet->value,
                Image::ATTRIBUTE_PATH => $fsFile,
            ]);

            $images[] = $image;
        }

        return $images;
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

    /**
     * Get the relation to the action models.
     *
     * @param  Image  $image
     * @return BelongsToMany
     */
    abstract protected function relation(Image $image): BelongsToMany;
}
