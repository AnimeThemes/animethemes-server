<?php

declare(strict_types=1);

namespace App\GraphQL\Controllers\Admin;

use App\GraphQL\Controllers\BaseController;
use App\Models\Admin\Announcement;
use GraphQL\Type\Definition\ResolveInfo;
use Illuminate\Database\Eloquent\Builder;

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
    public function index(Builder $builder, mixed $value, mixed $root, array $args, $context, ResolveInfo $resolveInfo): Builder
    {
        /** @phpstan-ignore-next-line */
        return $builder->public();
    }
}
