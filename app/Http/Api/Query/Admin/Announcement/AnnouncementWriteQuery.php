<?php

declare(strict_types=1);

namespace App\Http\Api\Query\Admin\Announcement;

use App\Http\Api\Query\Base\EloquentWriteQuery;
use App\Http\Api\Schema\Admin\AnnouncementSchema;
use App\Http\Api\Schema\EloquentSchema;
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
     * Get the resource schema.
     *
     * @return EloquentSchema
     */
    public function schema(): EloquentSchema
    {
        return new AnnouncementSchema();
    }

    /**
     * Get the query builder of the resource.
     *
     * @return Builder
     */
    public function builder(): Builder
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
