<?php

declare(strict_types=1);

namespace App\Http\Api\Query\Wiki\Video;

use App\Http\Api\Query\Base\EloquentReadQuery;
use App\Http\Api\Schema\EloquentSchema;
use App\Http\Api\Schema\Wiki\VideoSchema;
use App\Http\Resources\BaseCollection;
use App\Http\Resources\BaseResource;
use App\Http\Resources\Wiki\Collection\VideoCollection;
use App\Http\Resources\Wiki\Resource\VideoResource;
use App\Models\Wiki\Video;
use Illuminate\Database\Eloquent\Builder;

/**
 * Class VideoReadQuery.
 */
class VideoReadQuery extends EloquentReadQuery
{
    /**
     * Get the resource schema.
     *
     * @return EloquentSchema
     */
    public function schema(): EloquentSchema
    {
        return new VideoSchema();
    }

    /**
     * Get the query builder of the resource.
     *
     * @return Builder
     */
    public function indexBuilder(): Builder
    {
        return Video::query();
    }

    /**
     * Get the json resource.
     *
     * @param  mixed  $resource
     * @return BaseResource
     */
    public function resource(mixed $resource): BaseResource
    {
        return new VideoResource($resource, $this);
    }

    /**
     * Get the resource collection.
     *
     * @param  mixed  $resource
     * @return BaseCollection
     */
    public function collection(mixed $resource): BaseCollection
    {
        return new VideoCollection($resource, $this);
    }
}
