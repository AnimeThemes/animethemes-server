<?php

declare(strict_types=1);

namespace App\Http\Api\Query\List\Playlist\Track;

use App\Http\Api\Query\Base\EloquentWriteQuery;
use App\Http\Resources\BaseResource;
use App\Http\Resources\List\Playlist\Resource\TrackResource;
use App\Models\List\Playlist;
use App\Models\List\Playlist\PlaylistTrack;
use Illuminate\Database\Eloquent\Builder;

/**
 * Class TrackWriteQuery.
 */
class TrackWriteQuery extends EloquentWriteQuery
{
    /**
     * Create a new query instance.
     *
     * @param  Playlist  $playlist
     * @param  array  $parameters
     */
    public function __construct(protected readonly Playlist $playlist, array $parameters = [])
    {
        parent::__construct($parameters);
    }

    /**
     * Get the query builder of the resource.
     *
     * @return Builder
     */
    public function createBuilder(): Builder
    {
        return PlaylistTrack::query();
    }

    /**
     * Get the json resource.
     *
     * @param  mixed  $resource
     * @return BaseResource
     */
    public function resource(mixed $resource): BaseResource
    {
        return new TrackResource($resource, new TrackReadQuery($this->playlist));
    }
}
