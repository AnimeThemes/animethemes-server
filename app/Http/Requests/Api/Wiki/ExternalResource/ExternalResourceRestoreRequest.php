<?php

declare(strict_types=1);

namespace App\Http\Requests\Api\Wiki\ExternalResource;

use App\Http\Api\Query\Base\EloquentWriteQuery;
use App\Http\Api\Query\Wiki\ExternalResource\ExternalResourceWriteQuery;
use App\Http\Requests\Api\Base\EloquentRestoreRequest;

/**
 * Class ExternalResourceRestoreRequest.
 */
class ExternalResourceRestoreRequest extends EloquentRestoreRequest
{
    /**
     * Get the validation API Query.
     *
     * @return EloquentWriteQuery
     */
    public function getQuery(): EloquentWriteQuery
    {
        return new ExternalResourceWriteQuery($this->validated());
    }
}
