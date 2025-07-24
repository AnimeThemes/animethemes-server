<?php

declare(strict_types=1);

namespace App\GraphQL\Builders\Admin;

use App\Enums\Http\Api\Filter\ComparisonOperator;
use App\Models\Admin\FeaturedTheme;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Date;
use Nuwave\Lighthouse\Execution\ResolveInfo;
use Nuwave\Lighthouse\Support\Contracts\GraphQLContext;

class FeaturedThemeBuilder
{
    /**
     * Apply the query builder to the index query.
     *
     * @param  Builder<FeaturedTheme>  $builder
     * @param  mixed  $value
     * @param  mixed  $root
     * @param  array  $args
     * @param  GraphQLContext  $context
     * @param  ResolveInfo  $resolveInfo
     * @return Builder<FeaturedTheme>
     */
    public function index(Builder $builder, mixed $value, mixed $root, array $args, GraphQLContext $context, ResolveInfo $resolveInfo): Builder
    {
        return $builder->whereNotNull(FeaturedTheme::ATTRIBUTE_START_AT)
            ->whereDate(FeaturedTheme::ATTRIBUTE_START_AT, ComparisonOperator::LTE->value, Date::now());
    }

    /**
     * Apply the query builder to the index query.
     *
     * @param  Builder<FeaturedTheme>  $builder
     * @param  mixed  $value
     * @param  mixed  $root
     * @param  array  $args
     * @param  GraphQLContext  $context
     * @param  ResolveInfo  $resolveInfo
     * @return Builder<FeaturedTheme>
     */
    public function current(Builder $builder, mixed $value, mixed $root, array $args, GraphQLContext $context, ResolveInfo $resolveInfo): Builder
    {
        return $builder->whereNotNull(FeaturedTheme::ATTRIBUTE_START_AT)
            ->whereNotNull(FeaturedTheme::ATTRIBUTE_END_AT)
            ->whereDate(FeaturedTheme::ATTRIBUTE_START_AT, ComparisonOperator::LTE->value, Date::now())
            ->whereDate(FeaturedTheme::ATTRIBUTE_END_AT, ComparisonOperator::GT->value, Date::now());
    }
}
