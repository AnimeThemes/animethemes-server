<?php

declare(strict_types=1);

namespace App\GraphQL\Definition\Queries\Auth\User;

use App\GraphQL\Attributes\Resolvers\UseAuthDirective;
use App\GraphQL\Definition\Argument\Argument;
use App\GraphQL\Definition\Queries\BaseQuery;
use App\GraphQL\Definition\Types\Auth\User\MeType;

#[UseAuthDirective]
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
    public function baseType(): MeType
    {
        return new MeType();
    }
}
