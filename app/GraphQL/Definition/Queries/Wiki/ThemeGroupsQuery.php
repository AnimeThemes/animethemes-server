<?php

declare(strict_types=1);

namespace App\GraphQL\Definition\Queries\Wiki;

use App\GraphQL\Definition\Queries\EloquentQuery;
use App\GraphQL\Definition\Types\Wiki\ThemeGroupType;

/**
 * Class ThemeGroupsQuery.
 */
class ThemeGroupsQuery extends EloquentQuery
{
    public function __construct()
    {
        parent::__construct('themegroups');
    }

    /**
     * The description of the type.
     *
     * @return string
     */
    public function description(): string
    {
        return 'Returns a listing of theme groups resources given fields.';
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

            'orderBy: _ @orderBy(columnsEnum: "ThemeGroupColumnsOrderable")',
        ];
    }

    /**
     * The base return type of the query.
     *
     * @return ThemeGroupType
     */
    public function baseType(): ThemeGroupType
    {
        return new ThemeGroupType();
    }
}
