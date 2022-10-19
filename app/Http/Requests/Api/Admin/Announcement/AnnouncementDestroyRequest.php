<?php

declare(strict_types=1);

namespace App\Http\Requests\Api\Admin\Announcement;

use App\Http\Api\Query\Admin\Announcement\AnnouncementWriteQuery;
use App\Http\Api\Query\Base\EloquentWriteQuery;
use App\Http\Requests\Api\Base\EloquentDestroyRequest;

/**
 * Class AnnouncementDestroyRequest.
 */
class AnnouncementDestroyRequest extends EloquentDestroyRequest
{
    /**
     * Get the validation API Query.
     *
     * @return EloquentWriteQuery
     */
    public function getQuery(): EloquentWriteQuery
    {
        return new AnnouncementWriteQuery($this->validated());
    }
}
