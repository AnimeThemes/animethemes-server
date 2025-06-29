<?php

declare(strict_types=1);

namespace App\GraphQL\Definition\Queries\Wiki\Song;

use App\GraphQL\Definition\Queries\EloquentQuery;
use App\GraphQL\Definition\Types\Wiki\Song\MembershipType;

/**
 * Class MembershipsQuery.
 */
class MembershipsQuery extends EloquentQuery
{
    public function __construct()
    {
        parent::__construct('memberships');
    }

    /**
     * The description of the type.
     *
     * @return string
     */
    public function description(): string
    {
        return 'Returns a listing of memberships resources given fields.';
    }

    /**
     * The arguments of the type.
     *
     * @return array<int, string>
     */
    public function arguments(): array
    {
        return [
            ...parent::arguments(),

            'orderBy: _ @orderBy(columnsEnum: "MembershipColumnsOrderable")',
        ];
    }

    /**
     * The base return type of the query.
     *
     * @return MembershipType
     */
    public function baseType(): MembershipType
    {
        return new MembershipType();
    }
}
