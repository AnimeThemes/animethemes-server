<?php

declare(strict_types=1);

namespace App\Http\Requests\Api\Wiki\Series;

use App\Http\Api\Query\Base\EloquentWriteQuery;
use App\Http\Api\Query\Wiki\Series\SeriesWriteQuery;
use App\Http\Requests\Api\Base\EloquentRestoreRequest;

/**
 * Class SeriesRestoreRequest.
 */
class SeriesRestoreRequest extends EloquentRestoreRequest
{
    /**
     * Get the validation API Query.
     *
     * @return EloquentWriteQuery
     */
    public function getQuery(): EloquentWriteQuery
    {
        return new SeriesWriteQuery($this->validated());
    }
}
