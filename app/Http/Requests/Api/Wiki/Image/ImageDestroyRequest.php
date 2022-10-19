<?php

declare(strict_types=1);

namespace App\Http\Requests\Api\Wiki\Image;

use App\Http\Api\Query\Base\EloquentWriteQuery;
use App\Http\Api\Query\Wiki\Image\ImageWriteQuery;
use App\Http\Requests\Api\Base\EloquentDestroyRequest;

/**
 * Class ImageDestroyRequest.
 */
class ImageDestroyRequest extends EloquentDestroyRequest
{
    /**
     * Get the validation API Query.
     *
     * @return EloquentWriteQuery
     */
    public function getQuery(): EloquentWriteQuery
    {
        return new ImageWriteQuery($this->validated());
    }
}
