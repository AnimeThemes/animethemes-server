<?php

declare(strict_types=1);

namespace App\Http\Api\Query\Admin\Announcement;

use App\Http\Api\Query\Base\EloquentWriteQuery;
use App\Http\Resources\Admin\Resource\AnnouncementResource;
use App\Http\Resources\BaseResource;
use App\Models\Admin\Announcement;
use Illuminate\Database\Eloquent\Builder;

/**
 * Class AnnouncementWriteQuery.
 */
class AnnouncementWriteQuery extends EloquentWriteQuery
{
    /**
     * Get the query builder of the resource.
     *
     * @return Builder
     */
    public function createBuilder(): Builder
    {
        return Announcement::query();
    }

    /**
     * Get the json resource.
     *
     * @param  mixed  $resource
     * @return BaseResource
     */
    public function resource(mixed $resource): BaseResource
    {
        return new AnnouncementResource($resource, new AnnouncementReadQuery());
    }
}
