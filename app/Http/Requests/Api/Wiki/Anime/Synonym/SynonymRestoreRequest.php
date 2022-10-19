<?php

declare(strict_types=1);

namespace App\Http\Requests\Api\Wiki\Anime\Synonym;

use App\Http\Api\Query\Base\EloquentWriteQuery;
use App\Http\Api\Query\Wiki\Anime\Synonym\SynonymWriteQuery;
use App\Http\Requests\Api\Base\EloquentRestoreRequest;

/**
 * Class SynonymRestoreRequest.
 */
class SynonymRestoreRequest extends EloquentRestoreRequest
{
    /**
     * Get the validation API Query.
     *
     * @return EloquentWriteQuery
     */
    public function getQuery(): EloquentWriteQuery
    {
        return new SynonymWriteQuery($this->validated());
    }
}
