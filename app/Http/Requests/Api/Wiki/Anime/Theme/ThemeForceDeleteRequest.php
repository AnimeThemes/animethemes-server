<?php

declare(strict_types=1);

namespace App\Http\Requests\Api\Wiki\Anime\Theme;

use App\Http\Api\Query\Base\EloquentWriteQuery;
use App\Http\Api\Query\Wiki\Anime\Theme\ThemeWriteQuery;
use App\Http\Requests\Api\Base\EloquentForceDeleteRequest;

/**
 * Class ThemeForceDeleteRequest.
 */
class ThemeForceDeleteRequest extends EloquentForceDeleteRequest
{
    /**
     * Get the validation API Query.
     *
     * @return EloquentWriteQuery
     */
    public function getQuery(): EloquentWriteQuery
    {
        return new ThemeWriteQuery($this->validated());
    }
}
