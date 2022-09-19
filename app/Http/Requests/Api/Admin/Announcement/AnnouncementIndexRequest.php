<?php

declare(strict_types=1);

namespace App\Http\Requests\Api\Admin\Announcement;

use App\Http\Api\Query\Admin\Announcement\AnnouncementReadQuery;
use App\Http\Api\Query\Base\EloquentReadQuery;
use App\Http\Api\Schema\Admin\AnnouncementSchema;
use App\Http\Api\Schema\EloquentSchema;
use App\Http\Requests\Api\Base\EloquentIndexRequest;

/**
 * Class AnnouncementIndexRequest.
 */
class AnnouncementIndexRequest extends EloquentIndexRequest
{
    /**
     * Get the schema.
     *
     * @return EloquentSchema
     */
    protected function schema(): EloquentSchema
    {
        return new AnnouncementSchema();
    }

    /**
     * Get the validation API Query.
     *
     * @return EloquentReadQuery
     */
    public function getQuery(): EloquentReadQuery
    {
        return new AnnouncementReadQuery($this->validated());
    }
}
