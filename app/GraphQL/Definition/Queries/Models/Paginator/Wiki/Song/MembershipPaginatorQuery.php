<?php

declare(strict_types=1);

namespace App\GraphQL\Definition\Queries\Models\Paginator\Wiki\Song;

use App\GraphQL\Attributes\Resolvers\UsePaginateDirective;
use App\GraphQL\Definition\Queries\Models\Paginator\EloquentPaginatorQuery;
use App\GraphQL\Definition\Types\Wiki\Song\MembershipType;

#[UsePaginateDirective]
class MembershipPaginatorQuery extends EloquentPaginatorQuery
{
    public function __construct()
    {
        parent::__construct('membershipPaginator');
    }

    /**
     * The description of the type.
     */
    public function description(): string
    {
        return 'Returns a listing of memberships resources given fields.';
    }

    /**
     * The base return type of the query.
     */
    public function baseType(): MembershipType
    {
        return new MembershipType();
    }
}
