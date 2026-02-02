<?php

declare(strict_types=1);

namespace App\Http\Resources\Auth\Resource;

use App\Http\Api\Schema\Auth\UserSchema;
use App\Http\Api\Schema\Schema;
use App\Http\Resources\BaseJsonResource;

class UserJsonResource extends BaseJsonResource
{
    /**
     * The "data" wrapper that should be applied.
     *
     * @var string|null
     */
    public static $wrap = 'user';

    /**
     * Get the resource schema.
     */
    protected function schema(): Schema
    {
        return new UserSchema();
    }
}
