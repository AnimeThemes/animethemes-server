<?php

declare(strict_types=1);

namespace App\GraphQL\Schema\Queries\Models\Pagination\Admin;

use App\Enums\Http\Api\Filter\ComparisonOperator;
use App\GraphQL\Schema\Queries\Models\Pagination\EloquentPaginationQuery;
use App\GraphQL\Schema\Types\Admin\FeaturedThemeType;
use App\Models\Admin\FeaturedTheme;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Date;

class FeaturedThemePaginationQuery extends EloquentPaginationQuery
{
    public function __construct()
    {
        parent::__construct('featuredthemePagination');
    }

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

    protected function query(Builder $builder, array $args): Builder
    {
        return $builder->whereNotNull(FeaturedTheme::ATTRIBUTE_START_AT)
            ->whereDate(FeaturedTheme::ATTRIBUTE_START_AT, ComparisonOperator::LTE->value, Date::now());
    }
}
