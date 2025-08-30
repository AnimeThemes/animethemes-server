<?php

declare(strict_types=1);

namespace App\GraphQL\Definition\Queries\Admin;

use App\GraphQL\Definition\Queries\BaseQuery;
use App\GraphQL\Definition\Types\Admin\FeaturedThemeType;
use App\GraphQL\Support\Argument\Argument;
use App\Models\Admin\FeaturedTheme;
use Closure;
use GraphQL\Type\Definition\ResolveInfo;
use Illuminate\Support\Facades\Date;

class CurrentFeaturedThemeQuery extends BaseQuery
{
    public function __construct()
    {
        parent::__construct('currentfeaturedtheme', true, false);
    }

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
    public function baseRebingType(): FeaturedThemeType
    {
        return new FeaturedThemeType();
    }

    /**
     * Resolve the query.
     *
     * @param  array<string, mixed>  $args
     * @return FeaturedTheme|null
     */
    public function resolve($root, array $args, $context, ResolveInfo $resolveInfo, Closure $getSelectFields)
    {
        $builder = FeaturedTheme::query();

        $builder->whereValueBetween(Date::now(), [
            FeaturedTheme::ATTRIBUTE_START_AT,
            FeaturedTheme::ATTRIBUTE_END_AT,
        ]);

        $this->constrainEagerLoads($builder, $resolveInfo, $this->baseRebingType());

        return $builder->first();
    }
}
