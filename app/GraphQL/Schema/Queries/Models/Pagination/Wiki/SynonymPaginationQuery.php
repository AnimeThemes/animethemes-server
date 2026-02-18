<?php

declare(strict_types=1);

namespace App\GraphQL\Schema\Queries\Models\Pagination\Wiki;

use App\GraphQL\Schema\Queries\Models\Pagination\EloquentPaginationQuery;
use App\GraphQL\Schema\Types\Wiki\SynonymType;

class SynonymPaginationQuery extends EloquentPaginationQuery
{
    public function __construct()
    {
        parent::__construct('synonymPagination');
    }

    public function description(): string
    {
        return 'Returns a listing of synonyms resources given fields.';
    }

    /**
     * The base return type of the query.
     */
    public function baseType(): SynonymType
    {
        return new SynonymType();
    }
}
