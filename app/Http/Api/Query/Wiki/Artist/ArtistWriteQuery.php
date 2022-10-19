<?php

declare(strict_types=1);

namespace App\Http\Api\Query\Wiki\Artist;

use App\Http\Api\Query\Base\EloquentWriteQuery;
use App\Http\Resources\BaseResource;
use App\Http\Resources\Wiki\Resource\ArtistResource;
use App\Models\Wiki\Artist;
use Illuminate\Database\Eloquent\Builder;

/**
 * Class ArtistWriteQuery.
 */
class ArtistWriteQuery extends EloquentWriteQuery
{
    /**
     * Get the query builder of the resource.
     *
     * @return Builder
     */
    public function createBuilder(): Builder
    {
        return Artist::query();
    }

    /**
     * Get the json resource.
     *
     * @param  mixed  $resource
     * @return BaseResource
     */
    public function resource(mixed $resource): BaseResource
    {
        return new ArtistResource($resource, new ArtistReadQuery());
    }
}
