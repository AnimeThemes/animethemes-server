<?php

declare(strict_types=1);

namespace App\GraphQL\Schema\Queries\Models\Pagination\Wiki;

use App\Contracts\GraphQL\Fields\DeprecatedField;
use App\GraphQL\Schema\Queries\Models\Pagination\EloquentPaginationQuery;
use App\GraphQL\Schema\Types\Wiki\SynonymType;

class SynonymPaginationQuery extends EloquentPaginationQuery implements DeprecatedField
{
    public function name(): string
    {
        return 'synonymPagination';
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

    public function deprecationReason(): string
    {
        return 'Internal use only';
    }
}
