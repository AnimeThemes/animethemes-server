<?php

declare(strict_types=1);

namespace App\Http\Requests\Api\Base;

use App\Http\Api\Query\Base\EloquentWriteQuery;
use App\Http\Requests\Api\DestroyRequest;

/**
 * Class EloquentDestroyRequest.
 */
abstract class EloquentDestroyRequest extends DestroyRequest
{
    /**
     * Get the validation API Query.
     *
     * @return EloquentWriteQuery
     */
    abstract public function getQuery(): EloquentWriteQuery;
}
