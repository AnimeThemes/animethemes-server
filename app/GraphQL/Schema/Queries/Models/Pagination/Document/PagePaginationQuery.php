<?php

declare(strict_types=1);

namespace App\GraphQL\Schema\Queries\Models\Pagination\Document;

use App\GraphQL\Schema\Queries\Models\Pagination\EloquentPaginationQuery;
use App\GraphQL\Schema\Types\Document\PageType;
use Illuminate\Database\Eloquent\Builder;

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
    public function baseType(): PageType
    {
        return new PageType();
    }

    protected function query(Builder $builder, array $args): Builder
    {
        /** @phpstan-ignore-next-line */
        return $builder->public();
    }
}
