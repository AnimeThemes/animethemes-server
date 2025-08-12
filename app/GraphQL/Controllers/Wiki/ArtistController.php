<?php

declare(strict_types=1);

namespace App\GraphQL\Controllers\Wiki;

use App\GraphQL\Controllers\BaseController;
use App\Models\Wiki\Artist;
use GraphQL\Type\Definition\ResolveInfo;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Arr;

/**
 * @extends BaseController<Artist>
 */
class ArtistController extends BaseController
{
    final public const ROUTE_SLUG = 'slug';

    /**
     * Apply the query builder to the show query.
     *
     * @param  Builder<Artist>  $builder
     * @param  array  $args
     * @return Builder<Artist>
     */
    public function show(Builder $builder, mixed $value, mixed $root, array $args, $context, ResolveInfo $resolveInfo): Builder
    {
        return $builder
            ->whereKey(Arr::get($args, self::ROUTE_SLUG)->getKey());
    }
}
