<?php

declare(strict_types=1);

namespace App\Http\Requests\Api;

use App\Http\Api\Query\EloquentQuery;

/**
 * Class EloquentShowRequest.
 */
abstract class EloquentShowRequest extends ShowRequest
{
    /**
     * Get the validation API Query.
     *
     * @return EloquentQuery
     */
    abstract public function getQuery(): EloquentQuery;
}
