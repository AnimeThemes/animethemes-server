<?php

declare(strict_types=1);

namespace App\Http\Api\Query\Wiki\Anime\Theme\Entry;

use App\Http\Api\Query\Base\EloquentWriteQuery;
use App\Http\Resources\BaseResource;
use App\Http\Resources\Wiki\Anime\Theme\Resource\EntryResource;
use App\Models\Wiki\Anime\Theme\AnimeThemeEntry;
use Illuminate\Database\Eloquent\Builder;

/**
 * Class EntryWriteQuery.
 */
class EntryWriteQuery extends EloquentWriteQuery
{
    /**
     * Get the query builder of the resource.
     *
     * @return Builder
     */
    public function createBuilder(): Builder
    {
        return AnimeThemeEntry::query();
    }

    /**
     * Get the json resource.
     *
     * @param  mixed  $resource
     * @return BaseResource
     */
    public function resource(mixed $resource): BaseResource
    {
        return new EntryResource($resource, new EntryReadQuery());
    }
}
