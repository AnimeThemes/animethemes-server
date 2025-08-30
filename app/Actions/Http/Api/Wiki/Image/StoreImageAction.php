<?php

declare(strict_types=1);

namespace App\Actions\Http\Api\Wiki\Image;

use App\Actions\Http\Api\StoreAction;
use App\Http\Api\Field\Wiki\Image\ImageFileField;
use App\Models\Wiki\Image;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Filesystem\FilesystemAdapter;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Storage;

/**
 * @extends StoreAction<Image>
 */
class StoreImageAction extends StoreAction
{
    /**
     * @param  Builder<Image>  $builder
     * @param  array  $parameters
     */
    public function store(Builder $builder, array $parameters): Image
    {
        $imageParameters = Arr::except($parameters, ImageFileField::ATTRIBUTE_FILE);

        $file = Arr::get($parameters, ImageFileField::ATTRIBUTE_FILE);
        if ($file instanceof UploadedFile) {
            /** @var FilesystemAdapter $fs */
            $fs = Storage::disk(Config::get('image.disk'));

            $fsFile = $fs->putFile('', $file);

            Arr::set($imageParameters, Image::ATTRIBUTE_PATH, $fsFile);
        }

        return parent::store($builder, $imageParameters);
    }
}
