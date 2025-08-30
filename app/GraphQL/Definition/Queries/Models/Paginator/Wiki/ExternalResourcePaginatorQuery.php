<?php

declare(strict_types=1);

namespace App\GraphQL\Definition\Queries\Models\Paginator\Wiki;

use App\GraphQL\Definition\Queries\Models\Paginator\EloquentPaginatorQuery;
use App\GraphQL\Definition\Types\Wiki\ExternalResourceType;

class ExternalResourcePaginatorQuery extends EloquentPaginatorQuery
{
    public function __construct()
    {
        parent::__construct('externalresourcePaginator');
    }

    public function description(): string
    {
        return 'Returns a listing of resources given fields.';
    }

    /**
     * The base return type of the query.
     */
    public function baseRebingType(): ExternalResourceType
    {
        return new ExternalResourceType();
    }
}
