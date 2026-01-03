<?php

declare(strict_types=1);

namespace App\GraphQL\Schema\Queries\Models\Pagination\Wiki;

use App\GraphQL\Argument\Argument;
use App\GraphQL\Argument\SearchArgument;
use App\GraphQL\Schema\Queries\Models\Pagination\EloquentPaginationQuery;
use App\GraphQL\Schema\Types\Wiki\ArtistType;

class ArtistPaginationQuery extends EloquentPaginationQuery
{
    public function __construct()
    {
        parent::__construct('artistPagination');
    }

    public function description(): string
    {
        return 'Returns a listing of artist resources given fields.';
    }

    /**
     * The base return type of the query.
     */
    public function baseType(): ArtistType
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
