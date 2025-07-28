<?php

declare(strict_types=1);

namespace App\GraphQL\Definition\Queries\List\Playlist;

use App\GraphQL\Attributes\Resolvers\UseBuilderDirective;
use App\GraphQL\Attributes\Resolvers\UsePaginateDirective;
use App\GraphQL\Controllers\List\Playlist\PlaylistTrackController;
use App\GraphQL\Definition\Fields\List\Playlist\PlaylistTrack\PlaylistTrackPlaylistField;
use App\GraphQL\Definition\Queries\EloquentQuery;
use App\GraphQL\Definition\Types\List\Playlist\PlaylistTrackType;
use App\GraphQL\Support\Argument;

#[UseBuilderDirective(PlaylistTrackController::class)]
#[UsePaginateDirective]
class PlaylistTracksQuery extends EloquentQuery
{
    public function __construct()
    {
        parent::__construct('playlisttracks');
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
    public function baseType(): PlaylistTrackType
    {
        return new PlaylistTrackType();
    }
}
