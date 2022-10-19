<?php

declare(strict_types=1);

namespace App\Http\Requests\Api\Wiki\Song;

use App\Http\Api\Query\Base\EloquentWriteQuery;
use App\Http\Api\Query\Wiki\Song\SongWriteQuery;
use App\Http\Requests\Api\Base\EloquentDestroyRequest;

/**
 * Class SongDestroyRequest.
 */
class SongDestroyRequest extends EloquentDestroyRequest
{
    /**
     * Get the validation API Query.
     *
     * @return EloquentWriteQuery
     */
    public function getQuery(): EloquentWriteQuery
    {
        return new SongWriteQuery($this->validated());
    }
}
