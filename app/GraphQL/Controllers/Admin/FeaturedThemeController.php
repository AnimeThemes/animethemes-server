<?php

declare(strict_types=1);

namespace App\GraphQL\Controllers\Admin;

use App\Enums\Http\Api\Filter\ComparisonOperator;
use App\GraphQL\Controllers\BaseController;
use App\Models\Admin\FeaturedTheme;
use GraphQL\Type\Definition\ResolveInfo;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Date;

/**
 * @extends BaseController<FeaturedTheme>
 */
class FeaturedThemeController extends BaseController
{
    final public const ROUTE_SLUG = 'id';

    /**
     * Apply the query builder to the index query.
     *
     * @param  Builder<FeaturedTheme>  $builder
     * @param  array  $args
     * @return Builder<FeaturedTheme>
     */
    public function index(Builder $builder, mixed $value, mixed $root, array $args, $context, ResolveInfo $resolveInfo): Builder
    {
        return $builder->whereNotNull(FeaturedTheme::ATTRIBUTE_START_AT)
            ->whereDate(FeaturedTheme::ATTRIBUTE_START_AT, ComparisonOperator::LTE->value, Date::now());
    }
}
