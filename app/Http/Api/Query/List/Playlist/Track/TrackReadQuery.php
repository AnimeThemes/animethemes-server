<?php

declare(strict_types=1);

namespace App\Http\Api\Query\List\Playlist\Track;

use App\Http\Api\Query\Base\EloquentReadQuery;
use App\Http\Api\Schema\EloquentSchema;
use App\Http\Api\Schema\List\Playlist\TrackSchema;
use App\Http\Resources\BaseCollection;
use App\Http\Resources\BaseResource;
use App\Http\Resources\List\Playlist\Collection\TrackCollection;
use App\Http\Resources\List\Playlist\Resource\TrackResource;
use App\Models\List\Playlist;
use App\Models\List\Playlist\PlaylistTrack;
use Illuminate\Database\Eloquent\Builder;

/**
 * Class TrackReadQuery.
 */
class TrackReadQuery extends EloquentReadQuery
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
        return new TrackSchema();
    }

    /**
     * Get the query builder of the resource.
     *
     * @return Builder
     */
    public function indexBuilder(): Builder
    {
        return PlaylistTrack::query()->where(PlaylistTrack::ATTRIBUTE_PLAYLIST, $this->playlist->getKey());
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
