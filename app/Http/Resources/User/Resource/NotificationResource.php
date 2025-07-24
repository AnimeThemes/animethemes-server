<?php

declare(strict_types=1);

namespace App\Http\Resources\User\Resource;

use App\Http\Api\Schema\Schema;
use App\Http\Api\Schema\User\NotificationSchema;
use App\Http\Resources\BaseResource;

class NotificationResource extends BaseResource
{
    /**
     * The "data" wrapper that should be applied.
     *
     * @var string|null
     */
    public static $wrap = 'notification';

    /**
     * Get the resource schema.
     */
    protected function schema(): Schema
    {
        return new NotificationSchema();
    }
}
