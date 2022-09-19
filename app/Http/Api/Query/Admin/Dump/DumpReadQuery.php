<?php

declare(strict_types=1);

namespace App\Http\Api\Query\Admin\Dump;

use App\Http\Api\Query\Base\EloquentReadQuery;
use App\Http\Api\Schema\Admin\DumpSchema;
use App\Http\Api\Schema\EloquentSchema;
use App\Http\Resources\Admin\Collection\DumpCollection;
use App\Http\Resources\Admin\Resource\DumpResource;
use App\Http\Resources\BaseCollection;
use App\Http\Resources\BaseResource;
use App\Models\Admin\Dump;
use Illuminate\Database\Eloquent\Builder;

/**
 * Class DumpReadQuery.
 */
class DumpReadQuery extends EloquentReadQuery
{
    /**
     * Get the resource schema.
     *
     * @return EloquentSchema
     */
    public function schema(): EloquentSchema
    {
        return new DumpSchema();
    }

    /**
     * Get the query builder of the resource.
     *
     * @return Builder
     */
    public function builder(): Builder
    {
        return Dump::query();
    }

    /**
     * Get the json resource.
     *
     * @param  mixed  $resource
     * @return BaseResource
     */
    public function resource(mixed $resource): BaseResource
    {
        return new DumpResource($resource, $this);
    }

    /**
     * Get the resource collection.
     *
     * @param  mixed  $resource
     * @return BaseCollection
     */
    public function collection(mixed $resource): BaseCollection
    {
        return new DumpCollection($resource, $this);
    }
}
