<?php

declare(strict_types=1);

namespace App\GraphQL\Definition\Queries\Models\Paginator\Wiki\Anime;

use App\GraphQL\Definition\Queries\Models\Paginator\EloquentPaginatorQuery;
use App\GraphQL\Definition\Types\Wiki\Anime\AnimeThemeType;
use App\GraphQL\Support\Argument\Argument;
use App\GraphQL\Support\Argument\SearchArgument;

class AnimeThemePaginatorQuery extends EloquentPaginatorQuery
{
    public function __construct()
    {
        parent::__construct('animethemePaginator');
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
