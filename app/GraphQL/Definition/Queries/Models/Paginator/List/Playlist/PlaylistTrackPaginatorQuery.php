<?php

declare(strict_types=1);

namespace App\GraphQL\Definition\Queries\Models\Paginator\List\Playlist;

use App\GraphQL\Definition\Fields\List\Playlist\PlaylistTrack\PlaylistTrackPlaylistField;
use App\GraphQL\Definition\Queries\Models\Paginator\EloquentPaginatorQuery;
use App\GraphQL\Definition\Types\List\Playlist\PlaylistTrackType;
use App\GraphQL\Support\Argument\Argument;

class PlaylistTrackPaginatorQuery extends EloquentPaginatorQuery
{
    public function __construct()
    {
        parent::__construct('playlisttrackPaginator');
    }

    /**
     * The description of the type.
     */
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
}
