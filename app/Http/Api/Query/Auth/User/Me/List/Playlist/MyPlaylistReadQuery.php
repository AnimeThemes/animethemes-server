<?php

declare(strict_types=1);

namespace App\Http\Api\Query\Auth\User\Me\List\Playlist;

use App\Http\Api\Query\Base\EloquentReadQuery;
use App\Http\Api\Schema\EloquentSchema;
use App\Http\Api\Schema\List\PlaylistSchema;
use App\Http\Resources\BaseCollection;
use App\Http\Resources\BaseResource;
use App\Http\Resources\List\Collection\PlaylistCollection;
use App\Http\Resources\List\Resource\PlaylistResource;
use App\Models\Auth\User;
use Illuminate\Database\Eloquent\Builder;

/**
 * Class MyPlaylistReadQuery.
 */
class MyPlaylistReadQuery extends EloquentReadQuery
{
    /**
     * Create a new query instance.
     *
     * @param  User  $user
     * @param  array  $parameters
     */
    public function __construct(protected readonly User $user, array $parameters = [])
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
        return new PlaylistSchema();
    }

    /**
     * Get the query builder of the resource.
     *
     * @return Builder
     */
    public function indexBuilder(): Builder
    {
        return $this->user->playlists()->getQuery();
    }

    /**
     * Get the json resource.
     *
     * @param  mixed  $resource
     * @return BaseResource
     */
    public function resource(mixed $resource): BaseResource
    {
        return new PlaylistResource($resource, $this);
    }

    /**
     * Get the resource collection.
     *
     * @param  mixed  $resource
     * @return BaseCollection
     */
    public function collection(mixed $resource): BaseCollection
    {
        return new PlaylistCollection($resource, $this);
    }
}
