<?php

declare(strict_types=1);

namespace App\GraphQL\Definition\Queries\Admin;

use App\GraphQL\Attributes\Resolvers\UseBuilderDirective;
use App\GraphQL\Attributes\Resolvers\UsePaginateDirective;
use App\GraphQL\Builders\Admin\FeaturedThemeBuilder;
use App\GraphQL\Definition\Queries\EloquentQuery;
use App\GraphQL\Definition\Types\Admin\FeaturedThemeType;

#[UseBuilderDirective(FeaturedThemeBuilder::class)]
#[UsePaginateDirective]
class FeaturedThemesQuery extends EloquentQuery
{
    public function __construct()
    {
        parent::__construct('featuredthemes');
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
