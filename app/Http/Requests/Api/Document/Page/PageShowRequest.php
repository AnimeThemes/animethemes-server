<?php

declare(strict_types=1);

namespace App\Http\Requests\Api\Document\Page;

use App\Http\Api\Query\Document\PageQuery;
use App\Http\Api\Query\EloquentQuery;
use App\Http\Api\Schema\Document\PageSchema;
use App\Http\Api\Schema\EloquentSchema;
use App\Http\Requests\Api\EloquentShowRequest;

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
    protected function getSchema(): EloquentSchema
    {
        return new PageSchema();
    }

    /**
     * Get the validation API Query.
     *
     * @return EloquentQuery
     */
    public function getQuery(): EloquentQuery
    {
        return new PageQuery($this->validated());
    }
}
