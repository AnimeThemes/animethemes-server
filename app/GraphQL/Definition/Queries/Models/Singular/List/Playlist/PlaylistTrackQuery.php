<?php

declare(strict_types=1);

namespace App\GraphQL\Definition\Queries\Models\Singular\List\Playlist;

use App\GraphQL\Definition\Queries\Models\Singular\EloquentSingularQuery;
use App\GraphQL\Definition\Types\List\Playlist\PlaylistTrackType;
use App\Models\List\Playlist\PlaylistTrack;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Arr;

class PlaylistTrackQuery extends EloquentSingularQuery
{
    public function __construct()
    {
        parent::__construct('playlisttrack');
    }

    public function description(): string
    {
        return 'Returns a playlist track resource.';
    }

    /**
     * The base return type of the query.
     */
    public function baseRebingType(): PlaylistTrackType
    {
        return new PlaylistTrackType();
    }

    /**
     * Manage the query.
     */
    protected function query(Builder $builder, array $args): Builder
    {
        $playlist = Arr::get($args, 'playlist');

        $builder->whereBelongsTo($playlist, PlaylistTrack::RELATION_PLAYLIST);

        return $builder;
    }
}
