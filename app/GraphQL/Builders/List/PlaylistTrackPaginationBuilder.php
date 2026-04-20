<?php

declare(strict_types=1);

namespace App\GraphQL\Builders\List;

use App\Models\List\Playlist;
use App\Models\List\Playlist\PlaylistTrack;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Arr;

class PlaylistTrackPaginationBuilder
{
    /**
     * @param  array<string, mixed>  $args
     */
    public function __invoke(Builder $builder, null $_, null $root, $args): Builder
    {
        /** @var Playlist $playlist */
        $playlist = Arr::get($args, 'playlist');

        $builder->where(PlaylistTrack::ATTRIBUTE_PLAYLIST, $playlist->getKey());

        return $builder;
    }
}
