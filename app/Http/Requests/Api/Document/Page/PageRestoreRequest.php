<?php

declare(strict_types=1);

namespace App\Http\Requests\Api\Document\Page;

use App\Http\Api\Query\Base\EloquentWriteQuery;
use App\Http\Api\Query\Document\Page\PageWriteQuery;
use App\Http\Requests\Api\Base\EloquentRestoreRequest;

/**
 * Class PageRestoreRequest.
 */
class PageRestoreRequest extends EloquentRestoreRequest
{
    /**
     * Get the validation API Query.
     *
     * @return EloquentWriteQuery
     */
    public function getQuery(): EloquentWriteQuery
    {
        return new PageWriteQuery($this->validated());
    }
}
