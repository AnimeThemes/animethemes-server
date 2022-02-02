<?php

declare(strict_types=1);

namespace App\Http\Requests\Api;

use App\Http\Api\Query\EloquentQuery;

/**
 * Class EloquentIndexRequest.
 */
abstract class EloquentIndexRequest extends IndexRequest
{
    /**
     * Get the validation API Query.
     *
     * @return EloquentQuery
     */
    abstract public function getQuery(): EloquentQuery;
}
