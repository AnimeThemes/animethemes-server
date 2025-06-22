<?php

declare(strict_types=1);

namespace App\GraphQL\Definition\Queries\Wiki;

use App\GraphQL\Definition\Queries\EloquentQuery;
use App\GraphQL\Definition\Types\Wiki\AnimeType;

/**
 * Class AnimesQuery.
 */
class AnimesQuery extends EloquentQuery
{
    public function __construct()
    {
        parent::__construct('animes');
    }

    /**
     * The description of the type.
     *
     * @return string
     */
    public function description(): string
    {
        return 'Returns a listing of anime resources given fields.';
    }

    /**
     * The arguments of the type.
     *
     * @return array<int, string>
     */
    public function arguments(): array
    {
        return [
            'search: String @search',

            ...parent::arguments(),

            'orderBy: _ @orderBy(columnsEnum: "AnimeColumnsOrderable")',
        ];
    }

    /**
     * The base return type of the query.
     *
     * @return AnimeType
     */
    public function baseType(): AnimeType
    {
        return new AnimeType();
    }
}
