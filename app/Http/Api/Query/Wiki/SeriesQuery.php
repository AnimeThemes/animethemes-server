<?php

declare(strict_types=1);

namespace App\Http\Api\Query\Wiki;

use App\Http\Api\Query\EloquentQuery;
use App\Http\Api\Schema\Schema;
use App\Http\Api\Schema\Wiki\SeriesSchema;
use App\Http\Resources\BaseCollection;
use App\Http\Resources\BaseResource;
use App\Http\Resources\Wiki\Collection\SeriesCollection;
use App\Http\Resources\Wiki\Resource\SeriesResource;
use App\Models\Wiki\Series;
use Illuminate\Database\Eloquent\Builder;

/**
 * Class SeriesQuery.
 */
class SeriesQuery extends EloquentQuery
{
    /**
     * Get the resource schema.
     *
     * @return Schema
     */
    public function schema(): Schema
    {
        return new SeriesSchema();
    }

    /**
     * Get the query builder of the resource.
     *
     * @return Builder
     */
    public function builder(): Builder
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
        return SeriesResource::make($resource, $this);
    }

    /**
     * Get the resource collection.
     *
     * @param  mixed  $resource
     * @return BaseCollection
     */
    public function collection(mixed $resource): BaseCollection
    {
        return SeriesCollection::make($resource, $this);
    }
}
