<?php

declare(strict_types=1);

namespace App\GraphQL\Definition\Queries\Models\Paginator\Wiki;

use App\GraphQL\Definition\Queries\Models\Paginator\EloquentPaginatorQuery;
use App\GraphQL\Definition\Types\Wiki\ThemeGroupType;

class ThemeGroupPaginatorQuery extends EloquentPaginatorQuery
{
    public function __construct()
    {
        parent::__construct('themegroupPaginator');
    }

    public function description(): string
    {
        return 'Returns a listing of theme groups resources given fields.';
    }

    /**
     * The base return type of the query.
     */
    public function baseRebingType(): ThemeGroupType
    {
        return new ThemeGroupType();
    }
}
