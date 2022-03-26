<?php

declare(strict_types=1);

namespace App\Http\Requests\Api\Wiki\ExternalResource;

use App\Http\Api\Query\Base\EloquentReadQuery;
use App\Http\Api\Query\Wiki\ExternalResource\ExternalResourceReadQuery;
use App\Http\Api\Schema\EloquentSchema;
use App\Http\Api\Schema\Wiki\ExternalResourceSchema;
use App\Http\Requests\Api\Base\EloquentShowRequest;

/**
 * Class ExternalResourceShowRequest.
 */
class ExternalResourceShowRequest extends EloquentShowRequest
{
    /**
     * Get the schema.
     *
     * @return EloquentSchema
     */
    protected function schema(): EloquentSchema
    {
        return new ExternalResourceSchema();
    }

    /**
     * Get the validation API Query.
     *
     * @return EloquentReadQuery
     */
    public function getQuery(): EloquentReadQuery
    {
        return new ExternalResourceReadQuery($this->validated());
    }
}
