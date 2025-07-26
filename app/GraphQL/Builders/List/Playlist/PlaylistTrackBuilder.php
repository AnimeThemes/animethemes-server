<?php

declare(strict_types=1);

namespace App\GraphQL\Builders\List\Playlist;

use App\Models\List\Playlist;
use App\Models\List\Playlist\PlaylistTrack;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Arr;
use Nuwave\Lighthouse\Execution\ResolveInfo;
use Nuwave\Lighthouse\Support\Contracts\GraphQLContext;

class PlaylistTrackBuilder
{
    /**
     * Apply the query builder to the index query.
     *
     * @param  Builder<PlaylistTrack>  $builder
     * @param  array<string, mixed>  $args
     * @return Builder<PlaylistTrack>
     */
    public function index(Builder $builder, mixed $value, mixed $root, array $args, GraphQLContext $context, ResolveInfo $resolveInfo): Builder
    {
        /** @var Playlist $playlist */
        $playlist = Arr::get($args, 'playlist');

        $builder->where(PlaylistTrack::ATTRIBUTE_PLAYLIST, $playlist->getKey());

        return $builder;
    }

    /**
     * Apply the query builder to the index query.
     *
     * @param  Builder<PlaylistTrack>  $builder
     * @param  array<string, mixed>  $args
     * @return Builder<PlaylistTrack>
     */
    public function show(Builder $builder, mixed $value, mixed $root, array $args, GraphQLContext $context, ResolveInfo $resolveInfo): Builder
    {
        /** @var Playlist $playlist */
        $playlist = Arr::get($args, 'playlist');

        /** @var PlaylistTrack $track */
        $track = Arr::get($args, 'id');

        $builder->where(PlaylistTrack::ATTRIBUTE_PLAYLIST, $playlist->getKey());
        $builder->whereKey($track->getKey());

        return $builder;
    }
}
