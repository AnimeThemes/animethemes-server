<?php

declare(strict_types=1);

namespace App\GraphQL\Builders\Admin;

use App\Models\Admin\Dump;
use Illuminate\Database\Eloquent\Builder;
use Nuwave\Lighthouse\Execution\ResolveInfo;
use Nuwave\Lighthouse\Support\Contracts\GraphQLContext;

class DumpBuilder
{
    /**
     * Apply the query builder to the index query.
     *
     * @param  Builder<Dump>  $builder
     * @param  mixed  $value
     * @param  mixed  $root
     * @param  array  $args
     * @param  GraphQLContext  $context
     * @param  ResolveInfo  $resolveInfo
     * @return Builder<Dump>
     */
    public function index(Builder $builder, mixed $value, mixed $root, array $args, GraphQLContext $context, ResolveInfo $resolveInfo): Builder
    {
        /** @phpstan-ignore-next-line */
        return $builder->onlySafeDumps();
    }
}
