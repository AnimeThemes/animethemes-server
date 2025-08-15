<?php

declare(strict_types=1);

namespace App\GraphQL\Definition\Queries\Models\Paginator\Admin;

use App\Enums\Http\Api\Filter\ComparisonOperator;
use App\GraphQL\Definition\Queries\Models\Paginator\EloquentPaginatorQuery;
use App\GraphQL\Definition\Types\Admin\FeaturedThemeType;
use App\Models\Admin\FeaturedTheme;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Date;

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
    public function baseRebingType(): FeaturedThemeType
    {
        return new FeaturedThemeType();
    }

    /**
     * Manage the query.
     */
    protected function query(Builder $builder, array $args): Builder
    {
        return $builder->whereNotNull(FeaturedTheme::ATTRIBUTE_START_AT)
            ->whereDate(FeaturedTheme::ATTRIBUTE_START_AT, ComparisonOperator::LTE->value, Date::now());
    }
}
