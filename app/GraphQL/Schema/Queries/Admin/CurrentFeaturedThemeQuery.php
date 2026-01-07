<?php

declare(strict_types=1);

namespace App\GraphQL\Schema\Queries\Admin;

use App\GraphQL\Argument\Argument;
use App\GraphQL\Schema\Queries\BaseQuery;
use App\GraphQL\Schema\Types\Admin\FeaturedThemeType;
use App\Models\Admin\FeaturedTheme;
use GraphQL\Type\Definition\ResolveInfo;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Date;

class CurrentFeaturedThemeQuery extends BaseQuery
{
    public function __construct()
    {
        parent::__construct('currentfeaturedtheme');
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
    public function baseType(): FeaturedThemeType
    {
        return new FeaturedThemeType();
    }

    /**
     * Resolve the query.
     *
     * @param  array<string, mixed>  $args
     * @return FeaturedTheme|null
     */
    public function resolve($root, array $args, $context, ResolveInfo $resolveInfo): ?Model
    {
        $builder = FeaturedTheme::query();

        $builder->whereValueBetween(Date::now(), [
            FeaturedTheme::ATTRIBUTE_START_AT,
            FeaturedTheme::ATTRIBUTE_END_AT,
        ]);

        $this->constrainEagerLoads($builder, $resolveInfo, $this->baseType());

        return $builder->first();
    }
}
