<?php

declare(strict_types=1);

namespace App\GraphQL\Definition\Queries\Models\Paginator\Wiki\Song;

use App\GraphQL\Definition\Queries\Models\Paginator\EloquentPaginatorQuery;
use App\GraphQL\Definition\Types\Wiki\Song\PerformanceType;

class PerformancePaginatorQuery extends EloquentPaginatorQuery
{
    public function __construct()
    {
        parent::__construct('performancePaginator');
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
