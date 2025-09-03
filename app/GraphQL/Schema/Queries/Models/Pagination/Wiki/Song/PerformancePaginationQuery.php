<?php

declare(strict_types=1);

namespace App\GraphQL\Schema\Queries\Models\Pagination\Wiki\Song;

use App\GraphQL\Schema\Queries\Models\Pagination\EloquentPaginationQuery;
use App\GraphQL\Schema\Types\Wiki\Song\PerformanceType;

class PerformancePaginationQuery extends EloquentPaginationQuery
{
    public function __construct()
    {
        parent::__construct('performancePagination');
    }

    public function description(): string
    {
        return 'Returns a listing of performances resources given fields.';
    }

    /**
     * The base return type of the query.
     */
    public function baseRebingType(): PerformanceType
    {
        return new PerformanceType();
    }
}
