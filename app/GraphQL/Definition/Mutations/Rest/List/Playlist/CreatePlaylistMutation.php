<?php

declare(strict_types=1);

namespace App\GraphQL\Definition\Mutations\Rest\List\Playlist;

use App\GraphQL\Attributes\Resolvers\UseFieldDirective;
use App\GraphQL\Controllers\List\PlaylistController;
use App\GraphQL\Definition\Mutations\Rest\CreateMutation;
use App\GraphQL\Definition\Types\List\PlaylistType;
use App\Models\List\Playlist;

#[UseFieldDirective(PlaylistController::class, 'store')]
class CreatePlaylistMutation extends CreateMutation
{
    public function __construct()
    {
        parent::__construct(Playlist::class);
    }

    /**
     * The description of the mutation.
     */
    public function description(): string
    {
        return 'Create playlist';
    }

    /**
     * The base return type of the query.
     */
    public function baseType(): PlaylistType
    {
        return new PlaylistType();
    }
}
