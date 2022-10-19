<?php

declare(strict_types=1);

namespace App\Http\Api\Query\List\Playlist;

use App\Http\Api\Query\Base\EloquentWriteQuery;
use App\Http\Resources\BaseResource;
use App\Http\Resources\List\Resource\PlaylistResource;
use App\Models\List\Playlist;
use Illuminate\Database\Eloquent\Builder;

/**
 * Class PlaylistWriteQuery.
 */
class PlaylistWriteQuery extends EloquentWriteQuery
{
    /**
     * Get the query builder of the resource.
     *
     * @return Builder
     */
    public function createBuilder(): Builder
    {
        return Playlist::query();
    }

    /**
     * Get the json resource.
     *
     * @param  mixed  $resource
     * @return BaseResource
     */
    public function resource(mixed $resource): BaseResource
    {
        return new PlaylistResource($resource, new PlaylistReadQuery());
    }
}
