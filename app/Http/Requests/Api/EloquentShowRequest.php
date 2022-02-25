<?php

declare(strict_types=1);

namespace App\Http\Requests\Api;

use App\Http\Api\Query\EloquentQuery;
use App\Http\Api\Schema\EloquentSchema;

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
     * @return EloquentQuery
     */
    abstract public function getQuery(): EloquentQuery;
}
