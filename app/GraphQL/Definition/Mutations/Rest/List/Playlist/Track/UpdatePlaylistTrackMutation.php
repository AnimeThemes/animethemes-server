<?php

declare(strict_types=1);

namespace App\GraphQL\Definition\Mutations\Rest\List\Playlist\Track;

use App\GraphQL\Attributes\UseField;
use App\GraphQL\Controllers\List\Playlist\PlaylistTrackController;
use App\GraphQL\Definition\Mutations\Rest\UpdateMutation;
use App\GraphQL\Definition\Types\List\Playlist\PlaylistTrackType;
use App\Models\List\Playlist\PlaylistTrack;

#[UseField(PlaylistTrackController::class, 'update')]
class UpdatePlaylistTrackMutation extends UpdateMutation
{
    /**
     * Create a new mutation instance.
     */
    public function __construct()
    {
        parent::__construct(PlaylistTrack::class);
    }

    /**
     * The description of the mutation.
     *
     * @return string
     */
    public function description(): string
    {
        return 'Update playlist track';
    }

    /**
     * The base return type of the query.
     *
     * @return PlaylistTrackType
     */
    public function baseType(): PlaylistTrackType
    {
        return new PlaylistTrackType();
    }
}
