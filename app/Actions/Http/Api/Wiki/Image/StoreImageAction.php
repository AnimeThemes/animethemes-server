<?php

declare(strict_types=1);

namespace App\Actions\Http\Api\Wiki\Image;

use App\Actions\Http\Api\StoreAction;
use App\Http\Api\Field\Wiki\Image\ImageFileField;
use App\Models\Wiki\Image;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Filesystem\FilesystemAdapter;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Storage;

/**
 * Class StoreImageAction.
 */
class StoreImageAction extends StoreAction
{
    /**
     * Store image.
     *
     * @param  Builder  $builder
     * @param  array  $parameters
     * @return Model
     */
    public function store(Builder $builder, array $parameters): Model
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
