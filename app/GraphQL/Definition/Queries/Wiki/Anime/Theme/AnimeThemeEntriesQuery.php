<?php

declare(strict_types=1);

namespace App\GraphQL\Definition\Queries\Wiki\Anime\Theme;

use App\GraphQL\Definition\Queries\EloquentQuery;
use App\GraphQL\Definition\Types\Wiki\Anime\Theme\AnimeThemeEntryType;
use GraphQL\Type\Definition\Type;

/**
 * Class AnimeThemeEntriesQuery.
 */
class AnimeThemeEntriesQuery extends EloquentQuery
{
    public function __construct()
    {
        parent::__construct('animethemeentries');
    }

    /**
     * The description of the type.
     *
     * @return string
     */
    public function description(): string
    {
        return 'Returns a listing of anime theme entries resources given fields.';
    }

    /**
     * The arguments of the type.
     *
     * @return array<int, string>
     */
    public function arguments(): array
    {
        return [
            ...parent::arguments(),

            'orderBy: _ @orderBy(columnsEnum: "AnimeThemeEntryColumnsOrderable")',
        ];
    }

    /**
     * The base return type of the query.
     *
     * @return Type
     */
    public function baseType(): Type
    {
        return new AnimeThemeEntryType();
    }
}
