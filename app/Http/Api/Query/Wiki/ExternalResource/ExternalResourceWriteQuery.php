<?php

declare(strict_types=1);

namespace App\Http\Api\Query\Wiki\ExternalResource;

use App\Http\Api\Query\Base\EloquentWriteQuery;
use App\Http\Resources\BaseResource;
use App\Http\Resources\Wiki\Resource\ExternalResourceResource;
use App\Models\Wiki\ExternalResource;
use Illuminate\Database\Eloquent\Builder;

/**
 * Class ExternalResourceWriteQuery.
 */
class ExternalResourceWriteQuery extends EloquentWriteQuery
{
    /**
     * Get the query builder of the resource.
     *
     * @return Builder
     */
    public function createBuilder(): Builder
    {
        return ExternalResource::query();
    }

    /**
     * Get the json resource.
     *
     * @param  mixed  $resource
     * @return BaseResource
     */
    public function resource(mixed $resource): BaseResource
    {
        return new ExternalResourceResource($resource, new ExternalResourceReadQuery());
    }
}
