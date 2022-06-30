<?php

declare(strict_types=1);

namespace App\Http\Api\Query\Pivot\AnimeImage;

use App\Http\Api\Query\Base\EloquentWriteQuery;
use App\Http\Api\Schema\EloquentSchema;
use App\Http\Api\Schema\Pivot\AnimeImageSchema;
use App\Http\Resources\BaseResource;
use App\Http\Resources\Pivot\Resource\AnimeImageResource;
use App\Pivots\AnimeImage;
use Illuminate\Database\Eloquent\Builder;

/**
 * Class AnimeImageWriteQuery.
 */
class AnimeImageWriteQuery extends EloquentWriteQuery
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
        return new AnimeImageResource($resource, new AnimeImageReadQuery());
    }
}
