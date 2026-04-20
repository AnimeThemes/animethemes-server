<?php

declare(strict_types=1);

namespace App\GraphQL\Builders\List;

use App\Models\List\Playlist;
use App\Models\List\Playlist\PlaylistTrack;
use ArrayAccess;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Arr;

class PlaylistTrackBuilder
{
    /**
     * @param  array<string, mixed>  $args
     */
    public function __invoke(Builder $builder, null $_, null $root, ArrayAccess|array $args): Builder
    {
        $playlist = Arr::string($args, 'playlist');

        $builder->whereRelation(PlaylistTrack::RELATION_PLAYLIST, Playlist::ATTRIBUTE_HASHID, $playlist);

        return $builder;
    }
}
