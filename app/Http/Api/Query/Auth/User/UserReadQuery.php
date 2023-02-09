<?php

declare(strict_types=1);

namespace App\Http\Api\Query\Auth\User;

use App\Http\Api\Query\Base\EloquentReadQuery;
use App\Http\Api\Schema\Auth\UserSchema;
use App\Http\Api\Schema\EloquentSchema;
use App\Http\Resources\Auth\Collection\UserCollection;
use App\Http\Resources\Auth\Resource\UserResource;
use App\Http\Resources\BaseCollection;
use App\Http\Resources\BaseResource;
use App\Models\Auth\User;
use Illuminate\Database\Eloquent\Builder;

/**
 * Class UserReadQuery.
 */
class UserReadQuery extends EloquentReadQuery
{
    /**
     * Get the resource schema.
     *
     * @return EloquentSchema
     */
    public function schema(): EloquentSchema
    {
        return new UserSchema();
    }

    /**
     * Get the query builder of the resource.
     *
     * @return Builder
     */
    public function indexBuilder(): Builder
    {
        return User::query();
    }

    /**
     * Get the json resource.
     *
     * @param  mixed  $resource
     * @return BaseResource
     */
    public function resource(mixed $resource): BaseResource
    {
        return new UserResource($resource, $this);
    }

    /**
     * Get the resource collection.
     *
     * @param  mixed  $resource
     * @return BaseCollection
     */
    public function collection(mixed $resource): BaseCollection
    {
        return new UserCollection($resource, $this);
    }
}
