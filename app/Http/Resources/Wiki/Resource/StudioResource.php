<?php

declare(strict_types=1);

namespace App\Http\Resources\Wiki\Resource;

use App\Http\Api\Schema\Schema;
use App\Http\Api\Schema\Wiki\StudioSchema;
use App\Http\Resources\BaseResource;

class StudioResource extends BaseResource
{
    /**
     * The "data" wrapper that should be applied.
     *
     * @var string|null
     */
    public static $wrap = 'studio';

    /**
     * Get the resource schema.
     */
    protected function schema(): Schema
    {
        return new StudioSchema();
    }
}
