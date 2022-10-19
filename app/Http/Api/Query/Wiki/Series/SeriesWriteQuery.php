<?php

declare(strict_types=1);

namespace App\Http\Api\Query\Wiki\Series;

use App\Http\Api\Query\Base\EloquentWriteQuery;
use App\Http\Resources\BaseResource;
use App\Http\Resources\Wiki\Resource\SeriesResource;
use App\Models\Wiki\Series;
use Illuminate\Database\Eloquent\Builder;

/**
 * Class SeriesWriteQuery.
 */
class SeriesWriteQuery extends EloquentWriteQuery
{
    /**
     * Get the query builder of the resource.
     *
     * @return Builder
     */
    public function createBuilder(): Builder
    {
        return Series::query();
    }

    /**
     * Get the json resource.
     *
     * @param  mixed  $resource
     * @return BaseResource
     */
    public function resource(mixed $resource): BaseResource
    {
        return new SeriesResource($resource, new SeriesReadQuery());
    }
}
