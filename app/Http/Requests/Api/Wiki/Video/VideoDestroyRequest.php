<?php

declare(strict_types=1);

namespace App\Http\Requests\Api\Wiki\Video;

use App\Http\Api\Query\Base\EloquentWriteQuery;
use App\Http\Api\Query\Wiki\Video\VideoWriteQuery;
use App\Http\Requests\Api\Base\EloquentDestroyRequest;

/**
 * Class VideoDestroyRequest.
 */
class VideoDestroyRequest extends EloquentDestroyRequest
{
    /**
     * Get the validation API Query.
     *
     * @return EloquentWriteQuery
     */
    public function getQuery(): EloquentWriteQuery
    {
        return new VideoWriteQuery($this->validated());
    }
}
