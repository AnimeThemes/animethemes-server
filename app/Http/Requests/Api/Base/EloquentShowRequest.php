<?php

declare(strict_types=1);

namespace App\Http\Requests\Api\Base;

use App\Http\Api\Query\Base\EloquentReadQuery;
use App\Http\Api\Schema\EloquentSchema;
use App\Http\Requests\Api\ShowRequest;

/**
 * Class EloquentShowRequest.
 */
abstract class EloquentShowRequest extends ShowRequest
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
     * @return EloquentReadQuery
     */
    abstract public function getQuery(): EloquentReadQuery;
}
