<?php

declare(strict_types=1);

namespace App\Http\Api\Query\Wiki\Anime;

use App\Http\Api\Query\Base\EloquentWriteQuery;
use App\Http\Api\Schema\EloquentSchema;
use App\Http\Api\Schema\Wiki\AnimeSchema;
use App\Http\Resources\BaseResource;
use App\Http\Resources\Wiki\Resource\AnimeResource;
use App\Models\Wiki\Anime;
use Illuminate\Database\Eloquent\Builder;

/**
 * Class AnimeWriteQuery.
 */
class AnimeWriteQuery extends EloquentWriteQuery
{
    /**
     * Get the resource schema.
     *
     * @return EloquentSchema
     */
    public function schema(): EloquentSchema
    {
        return new AnimeSchema();
    }

    /**
     * Get the query builder of the resource.
     *
     * @return Builder
     */
    public function builder(): Builder
    {
        return Anime::query();
    }

    /**
     * Get the json resource.
     *
     * @param  mixed  $resource
     * @return BaseResource
     */
    public function resource(mixed $resource): BaseResource
    {
        return AnimeResource::make($resource, new AnimeReadQuery());
    }
}
