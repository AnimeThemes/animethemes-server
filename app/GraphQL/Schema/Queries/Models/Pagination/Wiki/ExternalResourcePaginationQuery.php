<?php

declare(strict_types=1);

namespace App\GraphQL\Schema\Queries\Models\Pagination\Wiki;

use App\GraphQL\Schema\Queries\Models\Pagination\EloquentPaginationQuery;
use App\GraphQL\Schema\Types\Wiki\ExternalResourceType;

class ExternalResourcePaginationQuery extends EloquentPaginationQuery
{
    public function __construct()
    {
        parent::__construct('externalresourcePagination');
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
