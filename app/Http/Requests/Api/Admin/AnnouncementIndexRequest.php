<?php

declare(strict_types=1);

namespace App\Http\Requests\Api\Admin;

use App\Http\Api\Query;
use App\Http\Api\Schema\Admin\AnnouncementSchema;
use App\Http\Api\Schema\Schema;
use App\Http\Requests\Api\IndexRequest;
use App\Http\Resources\Admin\Collection\AnnouncementCollection;
use App\Http\Resources\BaseCollection;
use Illuminate\Http\Resources\MissingValue;

/**
 * Class AnnouncementIndexRequest.
 */
class AnnouncementIndexRequest extends IndexRequest
{
    /**
     * Get the underlying resource collection.
     *
     * @return BaseCollection
     */
    protected function getCollection(): BaseCollection
    {
        return AnnouncementCollection::make(new MissingValue(), Query::make());
    }

    /**
     * Get the schema.
     *
     * @return Schema
     */
    protected function getSchema(): Schema
    {
        return new AnnouncementSchema();
    }
}
