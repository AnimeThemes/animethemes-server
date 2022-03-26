<?php

declare(strict_types=1);

namespace App\Http\Requests\Api\Base;

use App\Http\Api\Query\Base\EloquentWriteQuery;
use App\Http\Api\Schema\EloquentSchema;
use App\Http\Requests\Api\StoreRequest;

/**
 * Class EloquentStoreRequest.
 */
abstract class EloquentStoreRequest extends StoreRequest
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
     * @return EloquentWriteQuery
     */
    abstract public function getQuery(): EloquentWriteQuery;

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
