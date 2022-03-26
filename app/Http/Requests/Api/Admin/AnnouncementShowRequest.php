<?php

declare(strict_types=1);

namespace App\Http\Requests\Api\Admin;

use App\Http\Api\Query\Admin\AnnouncementReadQuery;
use App\Http\Api\Query\Base\EloquentReadQuery;
use App\Http\Api\Schema\Admin\AnnouncementSchema;
use App\Http\Api\Schema\EloquentSchema;
use App\Http\Requests\Api\Base\EloquentShowRequest;

/**
 * Class AnnouncementShowRequest.
 */
class AnnouncementShowRequest extends EloquentShowRequest
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
