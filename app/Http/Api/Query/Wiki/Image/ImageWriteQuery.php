<?php

declare(strict_types=1);

namespace App\Http\Api\Query\Wiki\Image;

use App\Http\Api\Field\Wiki\Image\ImageFileField;
use App\Http\Api\Query\Base\EloquentWriteQuery;
use App\Http\Api\Schema\EloquentSchema;
use App\Http\Api\Schema\Wiki\ImageSchema;
use App\Http\Resources\BaseResource;
use App\Http\Resources\Wiki\Resource\ImageResource;
use App\Models\Wiki\Image;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Arr;

/**
 * Class ImageWriteQuery.
 */
class ImageWriteQuery extends EloquentWriteQuery
{
    /**
     * Store model.
     *
     * @return BaseResource
     *
     * @noinspection PhpMissingParentCallCommonInspection
     */
    public function store(): BaseResource
    {
        $imageParameters = Arr::except($this->parameters, ImageFileField::ATTRIBUTE_FILE);

        $file = Arr::get($this->parameters, ImageFileField::ATTRIBUTE_FILE);
        if ($file instanceof UploadedFile) {
            Arr::set($imageParameters, Image::ATTRIBUTE_SIZE, $file->getSize());
            Arr::set($imageParameters, Image::ATTRIBUTE_MIMETYPE, $file->getClientMimeType());
            Arr::set($imageParameters, Image::ATTRIBUTE_PATH, $file->store('', 'images'));
        }

        $model = $this->builder()->create($imageParameters);

        // Scout will load relations to refresh related search indices.
        $model->unsetRelations();

        // Columns with default values may be unset if not provided in the query string.
        $model->refresh();

        return $this->resource($model);
    }

    /**
     * Get the resource schema.
     *
     * @return EloquentSchema
     */
    public function schema(): EloquentSchema
    {
        return new ImageSchema();
    }

    /**
     * Get the query builder of the resource.
     *
     * @return Builder
     */
    public function builder(): Builder
    {
        return Image::query();
    }

    /**
     * Get the json resource.
     *
     * @param  mixed  $resource
     * @return BaseResource
     */
    public function resource(mixed $resource): BaseResource
    {
        return ImageResource::make($resource, new ImageReadQuery());
    }
}
