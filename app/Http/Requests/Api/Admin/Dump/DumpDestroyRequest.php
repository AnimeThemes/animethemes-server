<?php

declare(strict_types=1);

namespace App\Http\Requests\Api\Admin\Dump;

use App\Http\Api\Query\Admin\Dump\DumpWriteQuery;
use App\Http\Api\Query\Base\EloquentWriteQuery;
use App\Http\Requests\Api\Base\EloquentDestroyRequest;

/**
 * Class DumpDestroyRequest.
 */
class DumpDestroyRequest extends EloquentDestroyRequest
{
    /**
     * Get the validation API Query.
     *
     * @return EloquentWriteQuery
     */
    public function getQuery(): EloquentWriteQuery
    {
        return new DumpWriteQuery($this->validated());
    }
}
