<?php

declare(strict_types=1);

namespace App\Http\Requests\Api\Wiki\Anime;

use App\Http\Api\Query\Base\EloquentWriteQuery;
use App\Http\Api\Query\Wiki\Anime\AnimeWriteQuery;
use App\Http\Requests\Api\Base\EloquentRestoreRequest;

/**
 * Class AnimeRestoreRequest.
 */
class AnimeRestoreRequest extends EloquentRestoreRequest
{
    /**
     * Get the validation API Query.
     *
     * @return EloquentWriteQuery
     */
    public function getQuery(): EloquentWriteQuery
    {
        return new AnimeWriteQuery($this->validated());
    }
}
