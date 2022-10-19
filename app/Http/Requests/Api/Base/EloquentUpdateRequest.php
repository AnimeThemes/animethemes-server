<?php

declare(strict_types=1);

namespace App\Http\Requests\Api\Base;

use App\Http\Api\Query\Base\EloquentWriteQuery;
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
     * @return EloquentWriteQuery
     */
    abstract public function getQuery(): EloquentWriteQuery;
}
