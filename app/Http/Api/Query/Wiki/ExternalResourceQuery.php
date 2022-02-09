<?php

declare(strict_types=1);

namespace App\Http\Api\Query\Wiki;

use App\Http\Api\Query\EloquentQuery;
use App\Http\Api\Schema\Schema;
use App\Http\Api\Schema\Wiki\ExternalResourceSchema;
use App\Http\Resources\BaseCollection;
use App\Http\Resources\BaseResource;
use App\Http\Resources\Wiki\Collection\ExternalResourceCollection;
use App\Http\Resources\Wiki\Resource\ExternalResourceResource;
use App\Models\Wiki\ExternalResource;
use Illuminate\Database\Eloquent\Builder;

/**
 * Class ExternalResourceQuery.
 */
class ExternalResourceQuery extends EloquentQuery
{
    /**
     * Get the resource schema.
     *
     * @return Schema
     */
    public function schema(): Schema
    {
        return new ExternalResourceSchema();
    }

    /**
     * Get the query builder of the resource.
     *
     * @return Builder
     */
    public function builder(): Builder
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
        return ExternalResourceResource::make($resource, $this);
    }

    /**
     * Get the resource collection.
     *
     * @param  mixed  $resource
     * @return BaseCollection
     */
    public function collection(mixed $resource): BaseCollection
    {
        return ExternalResourceCollection::make($resource, $this);
    }
}
