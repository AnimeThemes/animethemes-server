<?php

declare(strict_types=1);

namespace App\GraphQL\Schema\Queries\Models\Pagination\Wiki\Anime;

use App\GraphQL\Argument\Argument;
use App\GraphQL\Argument\SearchArgument;
use App\GraphQL\Schema\Queries\Models\Pagination\EloquentPaginationQuery;
use App\GraphQL\Schema\Types\Wiki\Anime\AnimeThemeType;

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
    public function baseType(): AnimeThemeType
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
