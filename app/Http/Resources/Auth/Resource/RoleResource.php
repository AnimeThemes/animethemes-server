<?php

declare(strict_types=1);

namespace App\Http\Resources\Auth\Resource;

use App\Http\Api\Schema\Auth\RoleSchema;
use App\Http\Api\Schema\Schema;
use App\Http\Resources\BaseResource;

class RoleResource extends BaseResource
{
    /**
     * The "data" wrapper that should be applied.
     *
     * @var string|null
     */
    public static $wrap = 'role';

    /**
     * Get the resource schema.
     *
     * @return Schema
     */
    protected function schema(): Schema
    {
        return new RoleSchema();
    }
}
