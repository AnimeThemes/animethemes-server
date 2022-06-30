<?php

declare(strict_types=1);

namespace App\Http\Api\Query\Pivot\AnimeImage;

use App\Http\Api\Query\Base\EloquentReadQuery;
use App\Http\Api\Schema\EloquentSchema;
use App\Http\Api\Schema\Pivot\AnimeImageSchema;
use App\Http\Resources\BaseCollection;
use App\Http\Resources\BaseResource;
use App\Http\Resources\Pivot\Collection\AnimeImageCollection;
use App\Http\Resources\Pivot\Resource\AnimeImageResource;
use App\Pivots\AnimeImage;
use Illuminate\Database\Eloquent\Builder;

/**
 * Class AnimeImageReadQuery.
 */
class AnimeImageReadQuery extends EloquentReadQuery
{
    /**
     * Get the resource schema.
     *
     * @return EloquentSchema
     */
    public function schema(): EloquentSchema
    {
        return new AnimeImageSchema();
    }

    /**
     * Get the query builder of the resource.
     *
     * @return Builder
     */
    public function builder(): Builder
    {
        return AnimeImage::query();
    }

    /**
     * Get the json resource.
     *
     * @param  mixed  $resource
     * @return BaseResource
     */
    public function resource(mixed $resource): BaseResource
    {
        return new AnimeImageResource($resource, $this);
    }

    /**
     * Get the resource collection.
     *
     * @param  mixed  $resource
     * @return BaseCollection
     */
    public function collection(mixed $resource): BaseCollection
    {
        return new AnimeImageCollection($resource, $this);
    }
}
