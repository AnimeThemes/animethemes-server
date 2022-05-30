<?php

declare(strict_types=1);

namespace App\Http\Requests\Api\Document\Page;

use App\Http\Api\Query\Base\EloquentWriteQuery;
use App\Http\Api\Query\Document\Page\PageWriteQuery;
use App\Http\Api\Schema\Document\PageSchema;
use App\Http\Api\Schema\EloquentSchema;
use App\Http\Requests\Api\Base\EloquentStoreRequest;

/**
 * Class PageStoreRequest.
 */
class PageStoreRequest extends EloquentStoreRequest
{
    /**
     * Get the schema.
     *
     * @return EloquentSchema
     */
    protected function schema(): EloquentSchema
    {
        return new PageSchema();
    }

    /**
     * Get the validation API Query.
     *
     * @return EloquentWriteQuery
     */
    public function getQuery(): EloquentWriteQuery
    {
        return new PageWriteQuery($this->validated());
    }
}
