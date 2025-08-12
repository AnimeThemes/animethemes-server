<?php

declare(strict_types=1);

namespace App\GraphQL\Controllers\Admin;

use App\GraphQL\Controllers\BaseController;
use App\Models\Admin\FeaturedTheme;
use GraphQL\Type\Definition\ResolveInfo;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Date;

/**
 * @extends BaseController<FeaturedTheme>
 */
class CurrentFeaturedThemeController extends BaseController
{
    final public const ROUTE_SLUG = 'id';

    /**
     * Apply the query builder to the show query.
     *
     * @param  Builder<FeaturedTheme>  $builder
     * @param  array  $args
     * @return Builder<FeaturedTheme>
     */
    public function show(Builder $builder, mixed $value, mixed $root, array $args, $context, ResolveInfo $resolveInfo): Builder
    {
        return $builder
            ->whereValueBetween(Date::now(), [
                FeaturedTheme::ATTRIBUTE_START_AT,
                FeaturedTheme::ATTRIBUTE_END_AT,
            ]);
    }
}
