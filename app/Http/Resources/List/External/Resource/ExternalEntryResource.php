<?php

declare(strict_types=1);

namespace App\Http\Resources\List\External\Resource;

use App\Http\Api\Schema\List\External\ExternalEntrySchema;
use App\Http\Api\Schema\Schema;
use App\Http\Resources\BaseResource;

/**
 * Class ExternalEntryResource.
 */
class ExternalEntryResource extends BaseResource
{
    /**
     * The "data" wrapper that should be applied.
     *
     * @var string|null
     */
    public static $wrap = 'external_entry';

    /**
     * Get the resource schema.
     *
     * @return Schema
     */
    protected function schema(): Schema
    {
        return new ExternalEntrySchema();
    }
}
