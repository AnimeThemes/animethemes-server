<?php

declare(strict_types=1);

namespace App\GraphQL\Schema\Queries\Models\Pagination\Wiki;

use App\GraphQL\Schema\Queries\Models\Pagination\EloquentPaginationQuery;
use App\GraphQL\Schema\Types\Wiki\SeriesType;
use App\GraphQL\Support\Argument\Argument;
use App\GraphQL\Support\Argument\SearchArgument;

class SeriesPaginationQuery extends EloquentPaginationQuery
{
    public function __construct()
    {
        parent::__construct('seriesPagination');
    }

    public function description(): string
    {
        return 'Returns a listing of series resources given fields.';
    }

    /**
     * The base return type of the query.
     */
    public function baseType(): SeriesType
    {
        return new SeriesType();
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
