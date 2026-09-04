<?php

declare(strict_types=1);

namespace App\Http\Resources\Wiki\Resource;

use App\Http\Api\Schema\Schema;
use App\Http\Api\Schema\Wiki\PerformanceSchema;
use App\Http\Resources\BaseJsonResource;

class PerformanceJsonResource extends BaseJsonResource
{
    /**
     * The "data" wrapper that should be applied.
     *
     * @var string|null
     */
    public static $wrap = 'performance';

    /**
     * Get the resource schema.
     */
    protected function schema(): Schema
    {
        return new PerformanceSchema();
    }
}
