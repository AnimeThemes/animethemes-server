<?php

declare(strict_types=1);

namespace App\Http\Api\Query\Wiki\Song;

use App\Http\Api\Query\Base\EloquentWriteQuery;
use App\Http\Resources\BaseResource;
use App\Http\Resources\Wiki\Resource\SongResource;
use App\Models\Wiki\Song;
use Illuminate\Database\Eloquent\Builder;

/**
 * Class SongWriteQuery.
 */
class SongWriteQuery extends EloquentWriteQuery
{
    /**
     * Get the query builder of the resource.
     *
     * @return Builder
     */
    public function createBuilder(): Builder
    {
        return Song::query();
    }

    /**
     * Get the json resource.
     *
     * @param  mixed  $resource
     * @return BaseResource
     */
    public function resource(mixed $resource): BaseResource
    {
        return new SongResource($resource, new SongReadQuery());
    }
}
