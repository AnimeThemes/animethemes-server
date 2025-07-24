<?php

declare(strict_types=1);

namespace App\GraphQL\Builders\Admin;

use App\Constants\FeatureConstants;
use App\Models\Admin\Feature;
use Illuminate\Database\Eloquent\Builder;
use Nuwave\Lighthouse\Execution\ResolveInfo;
use Nuwave\Lighthouse\Support\Contracts\GraphQLContext;

class FeatureBuilder
{
    /**
     * Apply the query builder to the index query.
     *
     * @param  Builder<Feature>  $builder
     * @param  array  $args
     * @return Builder<Feature>
     */
    public function index(Builder $builder, mixed $value, mixed $root, array $args, GraphQLContext $context, ResolveInfo $resolveInfo): Builder
    {
        return $builder->where(Feature::ATTRIBUTE_SCOPE, FeatureConstants::NULL_SCOPE);
    }
}
