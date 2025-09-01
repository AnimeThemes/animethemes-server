<?php

declare(strict_types=1);

namespace App\GraphQL\Definition\Queries\Models\Pagination\Wiki\Song;

use App\GraphQL\Definition\Queries\Models\Pagination\EloquentPaginationQuery;
use App\GraphQL\Definition\Types\Wiki\Song\MembershipType;

class MembershipPaginationQuery extends EloquentPaginationQuery
{
    public function __construct()
    {
        parent::__construct('membershipPagination');
    }

    public function description(): string
    {
        return 'Returns a listing of memberships resources given fields.';
    }

    /**
     * The base return type of the query.
     */
    public function baseRebingType(): MembershipType
    {
        return new MembershipType();
    }
}
