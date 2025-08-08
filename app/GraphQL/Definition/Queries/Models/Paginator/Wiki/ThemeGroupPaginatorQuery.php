<?php

declare(strict_types=1);

namespace App\GraphQL\Definition\Queries\Models\Paginator\Wiki;

use App\GraphQL\Attributes\Resolvers\UsePaginateDirective;
use App\GraphQL\Definition\Queries\Models\Paginator\EloquentPaginatorQuery;
use App\GraphQL\Definition\Types\Wiki\ThemeGroupType;

#[UsePaginateDirective]
class ThemeGroupPaginatorQuery extends EloquentPaginatorQuery
{
    public function __construct()
    {
        parent::__construct('themegroupPaginator');
    }

    /**
     * The description of the type.
     */
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
