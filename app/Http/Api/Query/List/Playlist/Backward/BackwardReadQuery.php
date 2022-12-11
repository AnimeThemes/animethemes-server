<?php

declare(strict_types=1);

namespace App\Http\Api\Query\List\Playlist\Backward;

use App\Http\Api\Query\Base\EloquentReadQuery;
use App\Http\Api\Schema\EloquentSchema;
use App\Http\Api\Schema\List\Playlist\BackwardSchema;
use App\Http\Resources\BaseCollection;
use App\Http\Resources\BaseResource;
use App\Http\Resources\List\Playlist\Collection\TrackCollection;
use App\Http\Resources\List\Playlist\Resource\TrackResource;
use App\Models\List\Playlist;
use App\Models\List\Playlist\BackwardPlaylistTrack;
use App\Models\List\Playlist\PlaylistTrack;
use Illuminate\Database\Eloquent\Builder;

/**
 * Class BackwardReadQuery.
 */
class BackwardReadQuery extends EloquentReadQuery
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
     * Get the resource schema.
     *
     * @return EloquentSchema
     */
    public function schema(): EloquentSchema
    {
        return new BackwardSchema();
    }

    /**
     * Get the query builder of the resource.
     *
     * @return Builder
     */
    public function indexBuilder(): Builder
    {
        $constraint = function (Builder $query) {
            $query->where(PlaylistTrack::ATTRIBUTE_ID, $this->playlist->last_id);
        };

        return BackwardPlaylistTrack::query()->treeOf($constraint);
    }

    /**
     * Get the json resource.
     *
     * @param  mixed  $resource
     * @return BaseResource
     */
    public function resource(mixed $resource): BaseResource
    {
        return new TrackResource($resource, $this);
    }

    /**
     * Get the resource collection.
     *
     * @param  mixed  $resource
     * @return BaseCollection
     */
    public function collection(mixed $resource): BaseCollection
    {
        return new TrackCollection($resource, $this);
    }
}
