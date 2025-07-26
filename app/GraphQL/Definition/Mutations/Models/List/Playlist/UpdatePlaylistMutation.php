<?php

declare(strict_types=1);

namespace App\GraphQL\Definition\Mutations\Models\List\Playlist;

use App\GraphQL\Attributes\Resolvers\UseFieldDirective;
use App\GraphQL\Controllers\List\PlaylistController;
use App\GraphQL\Definition\Mutations\Models\UpdateMutation;
use App\GraphQL\Definition\Types\List\PlaylistType;
use App\Models\List\Playlist;

#[UseFieldDirective(PlaylistController::class, 'update')]
class UpdatePlaylistMutation extends UpdateMutation
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
        return 'Update playlist';
    }

    /**
     * The base return type of the query.
     */
    public function baseType(): PlaylistType
    {
        return new PlaylistType();
    }
}
