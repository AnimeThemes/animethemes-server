<?php

declare(strict_types=1);

namespace App\Http\Requests\Api\Admin;

use App\Http\Api\Query\Admin\AnnouncementWriteQuery;
use App\Http\Api\Query\Base\EloquentWriteQuery;
use App\Http\Api\Schema\Admin\AnnouncementSchema;
use App\Http\Api\Schema\EloquentSchema;
use App\Http\Requests\Api\Base\EloquentStoreRequest;

/**
 * Class AnnouncementStoreRequest.
 */
class AnnouncementStoreRequest extends EloquentStoreRequest
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
     * @return EloquentWriteQuery
     */
    public function getQuery(): EloquentWriteQuery
    {
        return new AnnouncementWriteQuery($this->validated());
    }
}
