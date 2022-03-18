<?php

declare(strict_types=1);

namespace App\Http\Requests\Api\Base;

use App\Http\Api\Query\EloquentQuery;
use App\Http\Api\Schema\EloquentSchema;
use App\Http\Requests\Api\IndexRequest;

/**
 * Class EloquentIndexRequest.
 */
abstract class EloquentIndexRequest extends IndexRequest
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
}