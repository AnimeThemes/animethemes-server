<?php

declare(strict_types=1);

namespace App\Http\Resources\Admin\Resource;

use App\Http\Api\Schema\Admin\AnnouncementSchema;
use App\Http\Api\Schema\Schema;
use App\Http\Resources\BaseResource;

/**
 * Class AnnouncementResource.
 */
class AnnouncementResource extends BaseResource
{
    /**
     * The "data" wrapper that should be applied.
     *
     * @var string|null
     */
    public static $wrap = 'announcement';

    /**
     * Get the resource schema.
     *
     * @return Schema
     */
    protected function schema(): Schema
    {
        return new AnnouncementSchema();
    }
}
