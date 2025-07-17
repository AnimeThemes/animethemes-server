<?php

declare(strict_types=1);

namespace App\GraphQL\Definition\Mutations\Rest\List;

use App\GraphQL\Attributes\UseField;
use App\GraphQL\Controllers\List\PlaylistController;
use App\GraphQL\Definition\Mutations\Rest\UpdateMutation;
use App\GraphQL\Definition\Types\List\PlaylistType;
use App\Models\List\Playlist;

/**
 * Class UpdatePlaylistMutation.
 */
#[UseField(PlaylistController::class, 'update')]
class UpdatePlaylistMutation extends UpdateMutation
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
        return 'Update playlist';
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
