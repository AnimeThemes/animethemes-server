<?php

declare(strict_types=1);

namespace App\Http\Requests\Api\Wiki\ExternalResource;

use App\Http\Api\Query\EloquentQuery;
use App\Http\Api\Query\Wiki\ExternalResourceQuery;
use App\Http\Api\Schema\Schema;
use App\Http\Api\Schema\Wiki\ExternalResourceSchema;
use App\Http\Requests\Api\EloquentIndexRequest;

/**
 * Class ExternalResourceIndexRequest.
 */
class ExternalResourceIndexRequest extends EloquentIndexRequest
{
    /**
     * Get the schema.
     *
     * @return Schema
     */
    protected function getSchema(): Schema
    {
        return new ExternalResourceSchema();
    }

    /**
     * Get the validation API Query.
     *
     * @return EloquentQuery
     */
    public function getQuery(): EloquentQuery
    {
        return ExternalResourceQuery::make($this->validated());
    }
}
