<?php

declare(strict_types=1);

namespace App\GraphQL\Controllers\Document;

use App\GraphQL\Controllers\BaseController;
use App\Models\Document\Page;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Arr;
use Nuwave\Lighthouse\Execution\ResolveInfo;
use Nuwave\Lighthouse\Support\Contracts\GraphQLContext;

/**
 * @extends BaseController<Page>
 */
class PageController extends BaseController
{
    final public const ROUTE_SLUG = 'slug';

    /**
     * Apply the query builder to the index query.
     *
     * @param  Builder<Page>  $builder
     * @param  array  $args
     * @return Builder<Page>
     */
    public function show(Builder $builder, mixed $value, mixed $root, array $args, GraphQLContext $context, ResolveInfo $resolveInfo): Builder
    {
        return $builder
            ->where(self::ROUTE_SLUG, Arr::get($args, self::ROUTE_SLUG));
    }
}
