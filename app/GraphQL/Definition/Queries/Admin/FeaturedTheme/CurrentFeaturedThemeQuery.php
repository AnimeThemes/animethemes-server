<?php

declare(strict_types=1);

namespace App\GraphQL\Definition\Queries\Admin\FeaturedTheme;

use App\GraphQL\Attributes\UseBuilderDirective;
use App\GraphQL\Builders\Admin\FeaturedThemeBuilder;
use App\GraphQL\Definition\Argument\Argument;
use App\GraphQL\Definition\Queries\BaseQuery;
use App\GraphQL\Definition\Types\Admin\FeaturedThemeType;

#[UseBuilderDirective(FeaturedThemeBuilder::class, 'current')]
class CurrentFeaturedThemeQuery extends BaseQuery
{
    public function __construct()
    {
        parent::__construct('currentfeaturedtheme', true, false, false);
    }

    /**
     * The description of the type.
     */
    public function description(): string
    {
        return 'Returns the first featured theme where the current date is between start_at and end_at dates.';
    }

    /**
     * The directives of the type.
     *
     * @return array<string, array>
     */
    public function directives(): array
    {
        return [
            'find' => [],

            ...parent::directives(),
        ];
    }

    /**
     * The arguments of the type.
     *
     * @return Argument[]
     */
    public function arguments(): array
    {
        return [];
    }

    /**
     * The base return type of the query.
     */
    public function baseType(): FeaturedThemeType
    {
        return new FeaturedThemeType();
    }
}
