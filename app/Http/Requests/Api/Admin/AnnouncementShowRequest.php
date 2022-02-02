<?php

declare(strict_types=1);

namespace App\Http\Requests\Api\Admin;

use App\Http\Api\Query\Admin\AnnouncementQuery;
use App\Http\Api\Query\EloquentQuery;
use App\Http\Api\Schema\Admin\AnnouncementSchema;
use App\Http\Api\Schema\Schema;
use App\Http\Requests\Api\EloquentShowRequest;

/**
 * Class AnnouncementShowRequest.
 */
class AnnouncementShowRequest extends EloquentShowRequest
{
    /**
     * Get the schema.
     *
     * @return Schema
     */
    protected function getSchema(): Schema
    {
        return new AnnouncementSchema();
    }

    /**
     * Get the validation API Query.
     *
     * @return EloquentQuery
     */
    public function getQuery(): EloquentQuery
    {
        return AnnouncementQuery::make($this->validated());
    }
}
