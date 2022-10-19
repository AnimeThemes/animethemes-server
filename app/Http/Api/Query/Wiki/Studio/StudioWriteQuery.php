<?php

declare(strict_types=1);

namespace App\Http\Api\Query\Wiki\Studio;

use App\Http\Api\Query\Base\EloquentWriteQuery;
use App\Http\Resources\BaseResource;
use App\Http\Resources\Wiki\Resource\StudioResource;
use App\Models\Wiki\Studio;
use Illuminate\Database\Eloquent\Builder;

/**
 * Class StudioWriteQuery.
 */
class StudioWriteQuery extends EloquentWriteQuery
{
    /**
     * Get the query builder of the resource.
     *
     * @return Builder
     */
    public function createBuilder(): Builder
    {
        return Studio::query();
    }

    /**
     * Get the json resource.
     *
     * @param  mixed  $resource
     * @return BaseResource
     */
    public function resource(mixed $resource): BaseResource
    {
        return new StudioResource($resource, new StudioReadQuery());
    }
}
