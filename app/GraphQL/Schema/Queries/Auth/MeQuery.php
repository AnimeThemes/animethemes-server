<?php

declare(strict_types=1);

namespace App\GraphQL\Schema\Queries\Auth;

use App\GraphQL\Argument\Argument;
use App\GraphQL\Schema\Queries\BaseQuery;
use App\GraphQL\Schema\Types\Auth\User\MeType;
use App\Models\Auth\User;
use Closure;
use GraphQL\Type\Definition\ResolveInfo;
use Illuminate\Support\Facades\Auth;

class MeQuery extends BaseQuery
{
    public function __construct()
    {
        parent::__construct('me');
    }

    /**
     * The arguments of the type.
     *
     * @return Argument[]
     */
    public function arguments(): array
    {
        return [];
    }

    public function description(): string
    {
        return 'Returns the data of the currently authenticated user.';
    }

    /**
     * The base return type of the query.
     */
    public function baseType(): MeType
    {
        return new MeType();
    }

    /**
     * Resolve the query.
     *
     * @return User|null
     */
    public function resolve($root, array $args, $context, ResolveInfo $resolveInfo, Closure $getSelectFields)
    {
        $builder = User::query()->whereKey(Auth::id());

        $this->constrainEagerLoads($builder, $resolveInfo, $this->baseType());

        return $builder->first();
    }
}
