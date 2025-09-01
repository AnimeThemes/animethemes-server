<?php

declare(strict_types=1);

namespace App\GraphQL\Definition\Queries\Models\Pagination\Document;

use App\GraphQL\Definition\Queries\Models\Pagination\EloquentPaginationQuery;
use App\GraphQL\Definition\Types\Document\PageType;

class PagePaginationQuery extends EloquentPaginationQuery
{
    public function __construct()
    {
        parent::__construct('pagePagination');
    }

    public function description(): string
    {
        return 'Returns a listing of page resources given fields.';
    }

    /**
     * The base return type of the query.
     */
    public function baseRebingType(): PageType
    {
        return new PageType();
    }
}
