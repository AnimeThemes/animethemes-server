<?php

declare(strict_types=1);

namespace App\GraphQL\Definition\Queries\Models\Paginator\List\Playlist;

use App\GraphQL\Definition\Fields\List\Playlist\PlaylistTrack\PlaylistTrackPlaylistField;
use App\GraphQL\Definition\Queries\Models\Paginator\EloquentPaginatorQuery;
use App\GraphQL\Definition\Types\List\Playlist\PlaylistTrackType;
use App\GraphQL\Support\Argument\Argument;
use App\Models\List\Playlist;
use App\Models\List\Playlist\PlaylistTrack;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Arr;

class PlaylistTrackPaginatorQuery extends EloquentPaginatorQuery
{
    public function __construct()
    {
        parent::__construct('playlisttrackPaginator');
    }

    public function description(): string
    {
        return 'Returns a listing of tracks for the playlist.';
    }

    /**
     * The arguments of the type.
     *
     * @return Argument[]
     */
    public function arguments(): array
    {
        return [
            ...parent::arguments(),

            ...($this->resolveBindArguments([new PlaylistTrackPlaylistField()])),
        ];
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
        /** @var Playlist $playlist */
        $playlist = Arr::get($args, 'playlist');

        $builder->where(PlaylistTrack::ATTRIBUTE_PLAYLIST, $playlist->getKey());

        return $builder;
    }
}
