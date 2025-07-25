<?php

declare(strict_types=1);

namespace App\GraphQL\Definition\Queries\Wiki\Song;

use App\GraphQL\Attributes\Resolvers\UsePaginateDirective;
use App\GraphQL\Definition\Queries\EloquentQuery;
use App\GraphQL\Definition\Types\Wiki\Song\MembershipType;

#[UsePaginateDirective]
class MembershipsQuery extends EloquentQuery
{
    public function __construct()
    {
        parent::__construct('memberships');
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
