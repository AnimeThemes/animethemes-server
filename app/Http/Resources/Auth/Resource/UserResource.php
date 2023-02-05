<?php

declare(strict_types=1);

namespace App\Http\Resources\Auth\Resource;

use App\Http\Api\Query\ReadQuery;
use App\Http\Api\Schema\Auth\UserSchema;
use App\Http\Api\Schema\Schema;
use App\Http\Resources\BaseResource;
use App\Models\Auth\User;
use Illuminate\Http\Resources\MissingValue;

/**
 * Class UserResource.
 */
class UserResource extends BaseResource
{
    /**
     * The "data" wrapper that should be applied.
     *
     * @var string|null
     */
    public static $wrap = 'user';

    /**
     * Create a new resource instance.
     *
     * @param  User | MissingValue | null  $user
     * @param  ReadQuery  $query
     * @return void
     */
    public function __construct(User|MissingValue|null $user, ReadQuery $query)
    {
        parent::__construct($user, $query);
    }

    /**
     * Get the resource schema.
     *
     * @return Schema
     */
    protected function schema(): Schema
    {
        return new UserSchema();
    }
}
