<?php

declare(strict_types=1);

namespace App\GraphQL\Controllers\Admin;

use App\Constants\FeatureConstants;
use App\GraphQL\Controllers\BaseController;
use App\Models\Admin\Feature;
use Illuminate\Database\Eloquent\Builder;
use Nuwave\Lighthouse\Execution\ResolveInfo;
use Nuwave\Lighthouse\Support\Contracts\GraphQLContext;

/**
 * @extends BaseController<Feature>
 */
class FeatureController extends BaseController
{
    final public const ROUTE_SLUG = 'id';

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
