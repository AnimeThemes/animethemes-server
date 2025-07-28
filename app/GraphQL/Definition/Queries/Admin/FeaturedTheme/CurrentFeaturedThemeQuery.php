<?php

declare(strict_types=1);

namespace App\GraphQL\Definition\Queries\Admin\FeaturedTheme;

use App\GraphQL\Attributes\Resolvers\UseBuilderDirective;
use App\GraphQL\Attributes\Resolvers\UseFindDirective;
use App\GraphQL\Controllers\Admin\CurrentFeaturedThemeController;
use App\GraphQL\Definition\Queries\BaseQuery;
use App\GraphQL\Definition\Types\Admin\FeaturedThemeType;
use App\GraphQL\Support\Argument;

#[UseBuilderDirective(CurrentFeaturedThemeController::class, 'show')]
#[UseFindDirective]
class CurrentFeaturedThemeQuery extends BaseQuery
{
    public function __construct()
    {
        parent::__construct('currentfeaturedtheme', true, false);
    }

    /**
     * The description of the type.
     */
    public function description(): string
    {
        return 'Returns the first featured theme where the current date is between start_at and end_at dates.';
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
