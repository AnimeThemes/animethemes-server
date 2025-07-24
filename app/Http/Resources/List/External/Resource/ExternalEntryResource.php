<?php

declare(strict_types=1);

namespace App\Http\Resources\List\External\Resource;

use App\Http\Api\Schema\List\External\ExternalEntrySchema;
use App\Http\Api\Schema\Schema;
use App\Http\Resources\BaseResource;

class ExternalEntryResource extends BaseResource
{
    /**
     * The "data" wrapper that should be applied.
     *
     * @var string|null
     */
    public static $wrap = 'externalentry';

    /**
     * Get the resource schema.
     */
    protected function schema(): Schema
    {
        return new ExternalEntrySchema();
    }
}
