<?php

declare(strict_types=1);

namespace App\Http\Requests\Api\Admin;

use App\Http\Api\Query;
use App\Http\Requests\Api\ShowRequest;
use App\Http\Resources\Admin\Resource\AnnouncementResource;
use App\Http\Resources\BaseResource;
use Illuminate\Http\Resources\MissingValue;

/**
 * Class AnnouncementShowRequest.
 */
class AnnouncementShowRequest extends ShowRequest
{
    /**
     * Get the underlying resource.
     *
     * @return BaseResource
     */
    protected function getResource(): BaseResource
    {
        return AnnouncementResource::make(new MissingValue(), Query::make());
    }
}
