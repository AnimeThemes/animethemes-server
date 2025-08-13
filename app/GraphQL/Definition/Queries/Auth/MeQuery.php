<?php

declare(strict_types=1);

namespace App\GraphQL\Definition\Queries\Auth;

use App\GraphQL\Definition\Queries\BaseQuery;
use App\GraphQL\Definition\Types\Auth\User\MeType;
use App\GraphQL\Support\Argument\Argument;
use App\Models\Auth\User;
use Closure;
use GraphQL\Type\Definition\ResolveInfo;
use Illuminate\Support\Facades\Auth;

class MeQuery extends BaseQuery
{
    public function __construct()
    {
        parent::__construct('me', true, false);
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

    /**
     * The description of the type.
     */
    public function description(): string
    {
        return 'Returns the data of the currently authenticated user.';
    }

    /**
     * The base return type of the query.
     */
    public function baseRebingType(): MeType
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
        return Auth::user();
    }
}
