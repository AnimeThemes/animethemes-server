<?php

declare(strict_types=1);

namespace App\Http\Api\Query\Wiki;

use App\Http\Api\Query\EloquentQuery;
use App\Http\Api\Schema\Schema;
use App\Http\Api\Schema\Wiki\AnimeSchema;
use App\Http\Resources\BaseCollection;
use App\Http\Resources\BaseResource;
use App\Http\Resources\Wiki\Collection\AnimeCollection;
use App\Http\Resources\Wiki\Resource\AnimeResource;
use App\Models\Wiki\Anime;
use Illuminate\Database\Eloquent\Builder;

/**
 * Class AnimeQuery.
 */
class AnimeQuery extends EloquentQuery
{
    /**
     * Get the resource schema.
     *
     * @return Schema
     */
    public function schema(): Schema
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
        return AnimeResource::make($resource, $this);
    }

    /**
     * Get the resource collection.
     *
     * @param  mixed  $resource
     * @return BaseCollection
     */
    public function collection(mixed $resource): BaseCollection
    {
        return AnimeCollection::make($resource, $this);
    }
}
