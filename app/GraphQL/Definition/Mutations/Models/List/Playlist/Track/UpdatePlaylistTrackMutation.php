<?php

declare(strict_types=1);

namespace App\GraphQL\Definition\Mutations\Models\List\Playlist\Track;

use App\GraphQL\Attributes\Resolvers\UseFieldDirective;
use App\GraphQL\Controllers\List\Playlist\PlaylistTrackController;
use App\GraphQL\Definition\Mutations\Models\UpdateMutation;
use App\GraphQL\Definition\Types\List\Playlist\PlaylistTrackType;
use App\Models\List\Playlist\PlaylistTrack;

#[UseFieldDirective(PlaylistTrackController::class, 'update')]
class UpdatePlaylistTrackMutation extends UpdateMutation
{
    public function __construct()
    {
        parent::__construct(PlaylistTrack::class);
    }

    /**
     * The description of the mutation.
     */
    public function description(): string
    {
        return 'Update playlist track';
    }

    /**
     * The base return type of the query.
     */
    public function baseType(): PlaylistTrackType
    {
        return new PlaylistTrackType();
    }
}
