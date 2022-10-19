<?php

declare(strict_types=1);

namespace App\Http\Api\Query\List\Playlist;

use App\Enums\Models\List\PlaylistVisibility;
use App\Http\Api\Query\Base\EloquentReadQuery;
use App\Http\Api\Schema\EloquentSchema;
use App\Http\Api\Schema\List\PlaylistSchema;
use App\Http\Resources\BaseCollection;
use App\Http\Resources\BaseResource;
use App\Http\Resources\List\Collection\PlaylistCollection;
use App\Http\Resources\List\Resource\PlaylistResource;
use App\Models\List\Playlist;
use Illuminate\Database\Eloquent\Builder;

/**
 * Class PlaylistReadQuery.
 */
class PlaylistReadQuery extends EloquentReadQuery
{
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
        return Playlist::query()->where(Playlist::ATTRIBUTE_VISIBILITY, PlaylistVisibility::PUBLIC);
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
