<?php

declare(strict_types=1);

namespace App\GraphQL\Controllers\Admin;

use App\GraphQL\Controllers\BaseController;
use App\Models\Admin\Announcement;
use Illuminate\Database\Eloquent\Builder;
use Nuwave\Lighthouse\Execution\ResolveInfo;
use Nuwave\Lighthouse\Support\Contracts\GraphQLContext;

/**
 * @extends BaseController<Announcement>
 */
class AnnouncementController extends BaseController
{
    final public const ROUTE_SLUG = 'id';

    /**
     * Apply the query builder to the index query.
     *
     * @param  Builder<Announcement>  $builder
     * @param  array  $args
     * @return Builder<Announcement>
     */
    public function index(Builder $builder, mixed $value, mixed $root, array $args, GraphQLContext $context, ResolveInfo $resolveInfo): Builder
    {
        /** @phpstan-ignore-next-line */
        return $builder->public();
    }
}
