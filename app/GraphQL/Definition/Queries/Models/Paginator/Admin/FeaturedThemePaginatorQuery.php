<?php

declare(strict_types=1);

namespace App\GraphQL\Definition\Queries\Models\Paginator\Admin;

use App\GraphQL\Attributes\Resolvers\UseBuilderDirective;
use App\GraphQL\Attributes\Resolvers\UsePaginateDirective;
use App\GraphQL\Controllers\Admin\FeaturedThemeController;
use App\GraphQL\Definition\Queries\Models\Paginator\EloquentPaginatorQuery;
use App\GraphQL\Definition\Types\Admin\FeaturedThemeType;

#[UseBuilderDirective(FeaturedThemeController::class)]
#[UsePaginateDirective]
class FeaturedThemePaginatorQuery extends EloquentPaginatorQuery
{
    public function __construct()
    {
        parent::__construct('featuredthemePaginator');
    }

    /**
     * The description of the type.
     */
    public function description(): string
    {
        return 'Returns a listing of featured theme resources given fields.';
    }

    /**
     * The base return type of the query.
     */
    public function baseType(): FeaturedThemeType
    {
        return new FeaturedThemeType();
    }
}
