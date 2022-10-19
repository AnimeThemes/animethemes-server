<?php

declare(strict_types=1);

namespace App\Http\Api\Query\Wiki\Image;

use App\Http\Api\Query\Base\EloquentReadQuery;
use App\Http\Api\Schema\EloquentSchema;
use App\Http\Api\Schema\Wiki\ImageSchema;
use App\Http\Resources\BaseCollection;
use App\Http\Resources\BaseResource;
use App\Http\Resources\Wiki\Collection\ImageCollection;
use App\Http\Resources\Wiki\Resource\ImageResource;
use App\Models\Wiki\Image;
use Illuminate\Database\Eloquent\Builder;

/**
 * Class ImageReadQuery.
 */
class ImageReadQuery extends EloquentReadQuery
{
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
    public function indexBuilder(): Builder
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
        return new ImageResource($resource, $this);
    }

    /**
     * Get the resource collection.
     *
     * @param  mixed  $resource
     * @return BaseCollection
     */
    public function collection(mixed $resource): BaseCollection
    {
        return new ImageCollection($resource, $this);
    }
}
