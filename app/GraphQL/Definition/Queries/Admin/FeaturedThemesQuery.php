<?php

declare(strict_types=1);

namespace App\GraphQL\Definition\Queries\Admin;

use App\GraphQL\Attributes\UseBuilder;
use App\GraphQL\Builders\Admin\FeaturedThemeBuilder;
use App\GraphQL\Definition\Queries\EloquentQuery;
use App\GraphQL\Definition\Types\Admin\FeaturedThemeType;

/**
 * Class FeaturedThemesQuery.
 */
#[UseBuilder(FeaturedThemeBuilder::class)]
class FeaturedThemesQuery extends EloquentQuery
{
    public function __construct()
    {
        parent::__construct('featuredthemes');
    }

    /**
     * The description of the type.
     *
     * @return string
     */
    public function description(): string
    {
        return 'Returns a listing of featured theme resources given fields.';
    }

    /**
     * The arguments of the type.
     *
     * @return string[]
     */
    public function arguments(): array
    {
        return [
            ...parent::arguments(),

            'orderBy: _ @orderBy(columnsEnum: "FeaturedThemeColumnsOrderable")',
        ];
    }

    /**
     * The base return type of the query.
     *
     * @return FeaturedThemeType
     */
    public function baseType(): FeaturedThemeType
    {
        return new FeaturedThemeType();
    }
}
