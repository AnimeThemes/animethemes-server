<?php

declare(strict_types=1);

namespace App\GraphQL\Builders\List;

use App\Enums\Models\List\PlaylistVisibility;
use App\Models\List\Playlist;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
use Nuwave\Lighthouse\Execution\ResolveInfo;
use Nuwave\Lighthouse\Support\Contracts\GraphQLContext;

class PlaylistBuilder
{
    /**
     * Apply the query builder to the index query.
     *
     * @param  Builder<Playlist>  $builder
     * @param  mixed  $value
     * @param  mixed  $root
     * @param  array  $args
     * @param  GraphQLContext  $context
     * @param  ResolveInfo  $resolveInfo
     * @return Builder<Playlist>
     */
    public function index(Builder $builder, mixed $value, mixed $root, array $args, GraphQLContext $context, ResolveInfo $resolveInfo): Builder
    {
        $builder->where(Playlist::ATTRIBUTE_VISIBILITY, PlaylistVisibility::PUBLIC->value);

        if ($user = Auth::user()) {
            return $builder->orWhereBelongsTo($user, Playlist::RELATION_USER);
        }

        return $builder;
    }
}
