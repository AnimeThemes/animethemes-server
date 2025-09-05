<?php

declare(strict_types=1);

namespace App\GraphQL\Schema\Queries\Models\Pagination\Wiki\Song;

use App\GraphQL\Schema\Queries\Models\Pagination\EloquentPaginationQuery;
use App\GraphQL\Schema\Types\Wiki\Song\MembershipType;

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
    public function baseType(): MembershipType
    {
        return new MembershipType();
    }
}
