<?php

declare(strict_types=1);

namespace App\Http\Api\Schema;

use App\Http\Api\Query\Query;
use App\Http\Resources\BaseCollection;
use App\Http\Resources\BaseResource;
use Illuminate\Support\Str;

/**
 * Class EloquentSchema.
 */
abstract class EloquentSchema extends Schema
{
    /**
     * Get the resource of the schema.
     *
     * @param  mixed  $resource
     * @param  Query  $query
     * @return BaseResource
     */
    public function resource(mixed $resource, Query $query): BaseResource
    {
        $resourceClass = Str::of(get_class($this))
            ->replace('Api\\Schema', 'Resources')
            ->replace('Schema', 'Resource')
            ->replaceLast('\\', '\\Resource\\')
            ->__toString();

        return new $resourceClass($resource, $query);
    }

    /**
     * Get the collection of the schema.
     *
     * @param  mixed  $resource
     * @param  Query  $query
     * @return BaseCollection
     */
    public function collection(mixed $resource, Query $query): BaseCollection
    {
        $collectionClass = Str::of(get_class($this))
            ->replace('Api\\Schema', 'Resources')
            ->replace('Schema', 'Collection')
            ->replaceLast('\\', '\\Collection\\')
            ->__toString();

        return new $collectionClass($resource, $query);
    }
}
