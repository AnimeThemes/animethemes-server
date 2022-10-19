<?php

declare(strict_types=1);

namespace App\Http\Requests\Api\Wiki\Studio;

use App\Http\Api\Query\Base\EloquentWriteQuery;
use App\Http\Api\Query\Wiki\Studio\StudioWriteQuery;
use App\Http\Requests\Api\Base\EloquentRestoreRequest;

/**
 * Class StudioRestoreRequest.
 */
class StudioRestoreRequest extends EloquentRestoreRequest
{
    /**
     * Get the validation API Query.
     *
     * @return EloquentWriteQuery
     */
    public function getQuery(): EloquentWriteQuery
    {
        return new StudioWriteQuery($this->validated());
    }
}
