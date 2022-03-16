<?php

declare(strict_types=1);

namespace App\Http\Requests\Api\Base;

use App\Http\Api\Query\EloquentQuery;
use App\Http\Api\Schema\EloquentSchema;
use App\Http\Requests\Api\UpdateRequest;

/**
 * Class EloquentUpdateRequest.
 */
abstract class EloquentUpdateRequest extends UpdateRequest
{
    /**
     * Get the schema.
     *
     * @return EloquentSchema
     */
    abstract protected function schema(): EloquentSchema;

    /**
     * Get the validation API Query.
     *
     * @return EloquentQuery
     */
    abstract public function getQuery(): EloquentQuery;

    /**
     * The arguments for the policy ability to authorize.
     *
     * @return string
     */
    protected function arguments(): string
    {
        return $this->schema()->model();
    }
}
