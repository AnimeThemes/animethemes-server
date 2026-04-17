<?php

declare(strict_types=1);

namespace App\GraphQL\Schema\Queries\Models\Pagination\Wiki\Song;

use App\Contracts\GraphQL\Fields\DeprecatedField;
use App\GraphQL\Schema\Queries\Models\Pagination\EloquentPaginationQuery;
use App\GraphQL\Schema\Types\Wiki\Song\PerformanceType;

class PerformancePaginationQuery extends EloquentPaginationQuery implements DeprecatedField
{
    public function name(): string
    {
        return 'performancePagination';
    }

    public function description(): string
    {
        return 'Returns a listing of performances resources given fields.';
    }

    /**
     * The base return type of the query.
     */
    public function baseType(): PerformanceType
    {
        return new PerformanceType();
    }

    public function deprecationReason(): string
    {
        return 'Internal use only';
    }
}
