<?php

declare(strict_types=1);

namespace App\GraphQL\Builders\List\Playlist;

use App\Models\List\Playlist;
use App\Models\List\Playlist\PlaylistTrack;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Arr;
use Nuwave\Lighthouse\Execution\ResolveInfo;
use Nuwave\Lighthouse\Support\Contracts\GraphQLContext;

/**
 * Class PlaylistTrackBuilder.
 */
class PlaylistTrackBuilder
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
        /** @var Playlist $playlist */
        $playlist = Arr::get($args, 'playlist');

        $builder->where(PlaylistTrack::ATTRIBUTE_PLAYLIST, $playlist->getKey());

        return $builder;
    }
}
