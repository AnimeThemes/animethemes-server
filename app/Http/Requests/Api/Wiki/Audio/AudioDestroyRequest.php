<?php

declare(strict_types=1);

namespace App\Http\Requests\Api\Wiki\Audio;

use App\Http\Api\Query\Base\EloquentWriteQuery;
use App\Http\Api\Query\Wiki\Audio\AudioWriteQuery;
use App\Http\Requests\Api\Base\EloquentDestroyRequest;

/**
 * Class AudioDestroyRequest.
 */
class AudioDestroyRequest extends EloquentDestroyRequest
{
    /**
     * Get the validation API Query.
     *
     * @return EloquentWriteQuery
     */
    public function getQuery(): EloquentWriteQuery
    {
        return new AudioWriteQuery($this->validated());
    }
}
