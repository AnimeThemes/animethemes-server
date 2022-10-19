<?php

declare(strict_types=1);

namespace App\Http\Api\Query\Pivot\Wiki\AnimeImage;

use App\Http\Api\Query\Base\EloquentWriteQuery;
use App\Http\Resources\BaseResource;
use App\Http\Resources\Pivot\Wiki\Resource\AnimeImageResource;
use App\Pivots\Wiki\AnimeImage;
use Illuminate\Database\Eloquent\Builder;

/**
 * Class AnimeImageWriteQuery.
 */
class AnimeImageWriteQuery extends EloquentWriteQuery
{
    /**
     * Get the query builder of the resource.
     *
     * @return Builder
     */
    public function createBuilder(): Builder
    {
        return AnimeImage::query();
    }

    /**
     * Get the json resource.
     *
     * @param  mixed  $resource
     * @return BaseResource
     */
    public function resource(mixed $resource): BaseResource
    {
        return new AnimeImageResource($resource, new AnimeImageReadQuery());
    }
}
