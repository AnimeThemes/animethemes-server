<?php

declare(strict_types=1);

namespace App\Http\Resources\List\Resource;

use App\Http\Api\Schema\List\ExternalProfileSchema;
use App\Http\Api\Schema\Schema;
use App\Http\Resources\BaseResource;

class ExternalProfileResource extends BaseResource
{
    /**
     * The "data" wrapper that should be applied.
     *
     * @var string|null
     */
    public static $wrap = 'externalprofile';

    /**
     * Get the resource schema.
     *
     * @return Schema
     */
    protected function schema(): Schema
    {
        return new ExternalProfileSchema();
    }
}
