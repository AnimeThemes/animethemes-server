<?php

declare(strict_types=1);

namespace App\Http\Api\Query\Wiki\Anime\Synonym;

use App\Http\Api\Query\Base\EloquentWriteQuery;
use App\Http\Api\Schema\EloquentSchema;
use App\Http\Api\Schema\Wiki\Anime\SynonymSchema;
use App\Http\Resources\BaseResource;
use App\Http\Resources\Wiki\Anime\Resource\SynonymResource;
use App\Models\Wiki\Anime\AnimeSynonym;
use Illuminate\Database\Eloquent\Builder;

/**
 * Class SynonymWriteQuery.
 */
class SynonymWriteQuery extends EloquentWriteQuery
{
    /**
     * Get the resource schema.
     *
     * @return EloquentSchema
     */
    public function schema(): EloquentSchema
    {
        return new SynonymSchema();
    }

    /**
     * Get the query builder of the resource.
     *
     * @return Builder
     */
    public function builder(): Builder
    {
        return AnimeSynonym::query();
    }

    /**
     * Get the json resource.
     *
     * @param  mixed  $resource
     * @return BaseResource
     */
    public function resource(mixed $resource): BaseResource
    {
        return new SynonymResource($resource, new SynonymReadQuery());
    }
}
