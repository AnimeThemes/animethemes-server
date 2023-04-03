<?php

declare(strict_types=1);

namespace App\Http\Resources\Auth\User\Resource;

use App\Http\Api\Schema\Auth\User\MySchema;
use App\Http\Api\Schema\Schema;
use App\Http\Resources\BaseResource;

/**
 * Class UserResource.
 */
class MyResource extends BaseResource
{
    /**
     * The "data" wrapper that should be applied.
     *
     * @var string|null
     */
    public static $wrap = 'user';

    /**
     * Get the resource schema.
     *
     * @return Schema
     */
    protected function schema(): Schema
    {
        return new MySchema();
    }
}
