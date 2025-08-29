<?php

declare(strict_types=1);

namespace App\GraphQL\Definition\Queries\Models\Paginator\Wiki;

use App\GraphQL\Definition\Queries\Models\Paginator\EloquentPaginatorQuery;
use App\GraphQL\Definition\Types\Wiki\ArtistType;
use App\GraphQL\Support\Argument\Argument;
use App\GraphQL\Support\Argument\SearchArgument;

class ArtistPaginatorQuery extends EloquentPaginatorQuery
{
    public function __construct()
    {
        parent::__construct('artistPaginator');
    }

    public function description(): string
    {
        return 'Returns a listing of artist resources given fields.';
    }

    /**
     * The base return type of the query.
     */
    public function baseRebingType(): ArtistType
    {
        return new ArtistType();
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
