<?php

declare(strict_types=1);

namespace App\GraphQL\Schema\Queries\Models\Pagination\Wiki;

use App\GraphQL\Schema\Queries\Models\Pagination\EloquentPaginationQuery;
use App\GraphQL\Schema\Types\Wiki\AnimeType;
use App\GraphQL\Support\Argument\Argument;
use App\GraphQL\Support\Argument\SearchArgument;

class AnimePaginationQuery extends EloquentPaginationQuery
{
    public function __construct()
    {
        parent::__construct('animePagination');
    }

    public function description(): string
    {
        return 'Returns a listing of anime resources given fields.';
    }

    /**
     * The base return type of the query.
     */
    public function baseRebingType(): AnimeType
    {
        return new AnimeType();
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
