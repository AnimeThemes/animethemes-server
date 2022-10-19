<?php

declare(strict_types=1);

namespace App\Http\Requests\Api\Wiki\Anime\Theme;

use App\Http\Api\Query\Base\EloquentWriteQuery;
use App\Http\Api\Query\Wiki\Anime\Theme\ThemeWriteQuery;
use App\Http\Requests\Api\Base\EloquentRestoreRequest;

/**
 * Class ThemeRestoreRequest.
 */
class ThemeRestoreRequest extends EloquentRestoreRequest
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
