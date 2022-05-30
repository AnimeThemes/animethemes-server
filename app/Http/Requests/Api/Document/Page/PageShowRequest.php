<?php

declare(strict_types=1);

namespace App\Http\Requests\Api\Document\Page;

use App\Http\Api\Query\Base\EloquentReadQuery;
use App\Http\Api\Query\Document\Page\PageReadQuery;
use App\Http\Api\Schema\Document\PageSchema;
use App\Http\Api\Schema\EloquentSchema;
use App\Http\Requests\Api\Base\EloquentShowRequest;

/**
 * Class PageShowRequest.
 */
class PageShowRequest extends EloquentShowRequest
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
     * @return EloquentReadQuery
     */
    public function getQuery(): EloquentReadQuery
    {
        return new PageReadQuery($this->validated());
    }
}
