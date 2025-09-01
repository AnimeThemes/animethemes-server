<?php

declare(strict_types=1);

namespace App\GraphQL\Definition\Queries\Models\Pagination\Wiki;

use App\GraphQL\Definition\Queries\Models\Pagination\EloquentPaginationQuery;
use App\GraphQL\Definition\Types\Wiki\StudioType;
use App\GraphQL\Support\Argument\Argument;
use App\GraphQL\Support\Argument\SearchArgument;

class StudioPaginationQuery extends EloquentPaginationQuery
{
    public function __construct()
    {
        parent::__construct('studioPagination');
    }

    public function description(): string
    {
        return 'Returns a listing of studio resources given fields.';
    }

    /**
     * The base return type of the query.
     */
    public function baseRebingType(): StudioType
    {
        return new StudioType();
    }

    /**
     * The arguments of the class resolve as customs class helper.
     *
     * @return Argument[]
     */
    public function arguments(): array
    {
        return [
            ...parent::arguments(),

            new SearchArgument(),
        ];
    }
}
