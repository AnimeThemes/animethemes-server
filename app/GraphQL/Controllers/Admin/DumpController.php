<?php

declare(strict_types=1);

namespace App\GraphQL\Controllers\Admin;

use App\GraphQL\Controllers\BaseController;
use App\Models\Admin\Dump;
use GraphQL\Type\Definition\ResolveInfo;
use Illuminate\Database\Eloquent\Builder;

/**
 * @extends BaseController<Dump>
 */
class DumpController extends BaseController
{
    final public const ROUTE_SLUG = 'id';

    /**
     * Apply the query builder to the index query.
     *
     * @param  Builder<Dump>  $builder
     * @param  array  $args
     * @return Builder<Dump>
     */
    public function index(Builder $builder, mixed $value, mixed $root, array $args, $context, ResolveInfo $resolveInfo): Builder
    {
        /** @phpstan-ignore-next-line */
        return $builder->onlySafeDumps();
    }
}
