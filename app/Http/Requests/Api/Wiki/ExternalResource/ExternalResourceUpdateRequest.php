<?php

declare(strict_types=1);

namespace App\Http\Requests\Api\Wiki\ExternalResource;

use App\Http\Api\Query\Base\EloquentWriteQuery;
use App\Http\Api\Query\Wiki\ExternalResource\ExternalResourceWriteQuery;
use App\Http\Api\Schema\EloquentSchema;
use App\Http\Api\Schema\Wiki\ExternalResourceSchema;
use App\Http\Requests\Api\Base\EloquentUpdateRequest;

/**
 * Class ExternalResourceUpdateRequest.
 */
class ExternalResourceUpdateRequest extends EloquentUpdateRequest
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
     * @return EloquentWriteQuery
     */
    public function getQuery(): EloquentWriteQuery
    {
        return new ExternalResourceWriteQuery($this->validated());
    }
}
