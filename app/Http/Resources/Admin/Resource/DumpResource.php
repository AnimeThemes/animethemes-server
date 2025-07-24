<?php

declare(strict_types=1);

namespace App\Http\Resources\Admin\Resource;

use App\Http\Api\Schema\Admin\DumpSchema;
use App\Http\Api\Schema\Schema;
use App\Http\Resources\BaseResource;

class DumpResource extends BaseResource
{
    final public const ATTRIBUTE_LINK = 'link';

    /**
     * The "data" wrapper that should be applied.
     *
     * @var string|null
     */
    public static $wrap = 'dump';

    /**
     * Get the resource schema.
     */
    protected function schema(): Schema
    {
        return new DumpSchema();
    }
}
