<?php

declare(strict_types=1);

namespace App\GraphQL\Definition\Queries\Wiki\Anime;

use App\GraphQL\Definition\Queries\EloquentQuery;
use App\GraphQL\Definition\Types\Wiki\Anime\AnimeThemeType;
use GraphQL\Type\Definition\Type;

/**
 * Class AnimeThemesQuery.
 */
class AnimeThemesQuery extends EloquentQuery
{
    public function __construct()
    {
        parent::__construct('animethemes');
    }

    /**
     * The description of the type.
     *
     * @return string
     */
    public function description(): string
    {
        return 'Returns a listing of anime themes resources given fields.';
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

            'orderBy: _ @orderBy(columnsEnum: "AnimeThemeColumnsOrderable")',
        ];
    }

    /**
     * The base return type of the query.
     *
     * @return Type
     */
    public function baseType(): Type
    {
        return new AnimeThemeType();
    }
}
