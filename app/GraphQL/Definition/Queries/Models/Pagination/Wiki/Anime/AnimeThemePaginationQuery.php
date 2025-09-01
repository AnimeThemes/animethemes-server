<?php

declare(strict_types=1);

namespace App\GraphQL\Definition\Queries\Models\Pagination\Wiki\Anime;

use App\GraphQL\Definition\Queries\Models\Pagination\EloquentPaginationQuery;
use App\GraphQL\Definition\Types\Wiki\Anime\AnimeThemeType;
use App\GraphQL\Support\Argument\Argument;
use App\GraphQL\Support\Argument\SearchArgument;

class AnimeThemePaginationQuery extends EloquentPaginationQuery
{
    public function __construct()
    {
        parent::__construct('animethemePagination');
    }

    public function description(): string
    {
        return 'Returns a listing of anime themes resources given fields.';
    }

    /**
     * The base return type of the query.
     */
    public function baseRebingType(): AnimeThemeType
    {
        return new AnimeThemeType();
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
