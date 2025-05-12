<?php

declare(strict_types=1);

namespace App\GraphQL\Builders\Admin;

use App\Enums\Http\Api\Filter\ComparisonOperator;
use App\Models\Admin\Dump;
use Illuminate\Database\Eloquent\Builder;
use Nuwave\Lighthouse\Execution\ResolveInfo;
use Nuwave\Lighthouse\Support\Contracts\GraphQLContext;

/**
 * Class DumpBuilder.
 */
class DumpBuilder
{
    /**
     * Apply the query builder to the index query.
     *
     * @param  Builder  $builder
     * @param  mixed  $value
     * @param  mixed  $root
     * @param  array  $args
     * @param  GraphQLContext  $context
     * @param  ResolveInfo  $resolveInfo
     * @return Builder
     */
    public function index(Builder $builder, mixed $value, mixed $root, array $args, GraphQLContext $context, ResolveInfo $resolveInfo): Builder
    {
        foreach (Dump::safeDumps() as $path) {
            $builder->orWhere(Dump::ATTRIBUTE_PATH, ComparisonOperator::LIKE->value, $path.'%');
        }

        return $builder;
    }
}
