<?php

declare(strict_types=1);

namespace App\GraphQL\Definition\Queries\Admin;

use App\GraphQL\Attributes\UseBuilder;
use App\GraphQL\Builders\Admin\FeaturedThemeBuilder;
use App\GraphQL\Definition\Queries\EloquentQuery;
use App\GraphQL\Definition\Types\Admin\FeaturedThemeType;

#[UseBuilder(FeaturedThemeBuilder::class)]
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
