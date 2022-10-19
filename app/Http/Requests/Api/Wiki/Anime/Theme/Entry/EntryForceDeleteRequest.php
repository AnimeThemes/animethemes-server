<?php

declare(strict_types=1);

namespace App\Http\Requests\Api\Wiki\Anime\Theme\Entry;

use App\Http\Api\Query\Base\EloquentWriteQuery;
use App\Http\Api\Query\Wiki\Anime\Theme\Entry\EntryWriteQuery;
use App\Http\Requests\Api\Base\EloquentForceDeleteRequest;

/**
 * Class EntryForceDeleteRequest.
 */
class EntryForceDeleteRequest extends EloquentForceDeleteRequest
{
    /**
     * Get the validation API Query.
     *
     * @return EloquentWriteQuery
     */
    public function getQuery(): EloquentWriteQuery
    {
        return new EntryWriteQuery($this->validated());
    }
}
