<?php

declare(strict_types=1);

namespace App\GraphQL\Schema\Queries\Models\Pagination\Wiki;

use App\GraphQL\Schema\Queries\Models\Pagination\EloquentPaginationQuery;
use App\GraphQL\Schema\Types\Wiki\ThemeGroupType;

class ThemeGroupPaginationQuery extends EloquentPaginationQuery
{
    public function name(): string
    {
        return 'themegroupPagination';
    }

    public function description(): string
    {
        return 'Returns a listing of theme groups resources given fields.';
    }

    /**
     * The base return type of the query.
     */
    public function baseType(): ThemeGroupType
    {
        return new ThemeGroupType();
    }
}
