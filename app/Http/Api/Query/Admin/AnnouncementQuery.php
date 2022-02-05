<?php

declare(strict_types=1);

namespace App\Http\Api\Query\Admin;

use App\Http\Api\Query\EloquentQuery;
use App\Http\Api\Schema\Admin\AnnouncementSchema;
use App\Http\Api\Schema\Schema;
use App\Http\Resources\Admin\Collection\AnnouncementCollection;
use App\Http\Resources\Admin\Resource\AnnouncementResource;
use App\Http\Resources\BaseCollection;
use App\Http\Resources\BaseResource;
use App\Models\Admin\Announcement;
use Illuminate\Database\Eloquent\Builder;

/**
 * Class AnnouncementQuery.
 */
class AnnouncementQuery extends EloquentQuery
{
    /**
     * Get the resource schema.
     *
     * @return Schema
     */
    public function schema(): Schema
    {
        return new AnnouncementSchema();
    }

    /**
     * Get the query builder of the resource.
     *
     * @return Builder|null
     */
    public function builder(): ?Builder
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
        return AnnouncementResource::make($resource, $this);
    }

    /**
     * Get the resource collection.
     *
     * @param  mixed  $resource
     * @return BaseCollection
     */
    public function collection(mixed $resource): BaseCollection
    {
        return AnnouncementCollection::make($resource, $this);
    }
}
