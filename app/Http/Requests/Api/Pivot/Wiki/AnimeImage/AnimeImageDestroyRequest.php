<?php

declare(strict_types=1);

namespace App\Http\Requests\Api\Pivot\Wiki\AnimeImage;

use App\Http\Api\Query\Base\EloquentWriteQuery;
use App\Http\Api\Query\Pivot\Wiki\AnimeImage\AnimeImageWriteQuery;
use App\Http\Requests\Api\Base\EloquentDestroyRequest;

/**
 * Class AnimeImageDestroyRequest.
 */
class AnimeImageDestroyRequest extends EloquentDestroyRequest
{
    /**
     * Get the validation API Query.
     *
     * @return EloquentWriteQuery
     */
    public function getQuery(): EloquentWriteQuery
    {
        return new AnimeImageWriteQuery($this->validated());
    }
}
