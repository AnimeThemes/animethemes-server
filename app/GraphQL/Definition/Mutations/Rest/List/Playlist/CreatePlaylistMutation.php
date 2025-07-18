<?php

declare(strict_types=1);

namespace App\GraphQL\Definition\Mutations\Rest\List\Playlist;

use App\GraphQL\Attributes\UseField;
use App\GraphQL\Controllers\List\PlaylistController;
use App\GraphQL\Definition\Mutations\Rest\CreateMutation;
use App\GraphQL\Definition\Types\List\PlaylistType;
use App\Models\List\Playlist;

/**
 * Class CreatePlaylistMutation.
 */
#[UseField(PlaylistController::class, 'store')]
class CreatePlaylistMutation extends CreateMutation
{
    /**
     * Create a new mutation instance.
     */
    public function __construct()
    {
        parent::__construct(Playlist::class);
    }

    /**
     * The description of the mutation.
     *
     * @return string
     */
    public function description(): string
    {
        return 'Create playlist';
    }

    /**
     * The base return type of the query.
     *
     * @return PlaylistType
     */
    public function baseType(): PlaylistType
    {
        return new PlaylistType();
    }
}
