<?php

declare(strict_types=1);

namespace App\GraphQL\Controllers\Document;

use App\GraphQL\Controllers\BaseController;
use App\Models\Document\Page;
use GraphQL\Type\Definition\ResolveInfo;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Arr;

/**
 * @extends BaseController<Page>
 */
class PageController extends BaseController
{
    final public const ROUTE_SLUG = 'slug';

    /**
     * Apply the query builder to the show query.
     *
     * @param  Builder<Page>  $builder
     * @param  array  $args
     * @return Builder<Page>
     */
    public function show(Builder $builder, mixed $value, mixed $root, array $args, $context, ResolveInfo $resolveInfo): Builder
    {
        return $builder
            ->where(self::ROUTE_SLUG, Arr::get($args, self::ROUTE_SLUG));
    }
}
