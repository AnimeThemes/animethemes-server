<?php

declare(strict_types=1);

namespace App\GraphQL\Definition\Queries\Models\Singular\List\Playlist;

use App\GraphQL\Attributes\Resolvers\UseBuilderDirective;
use App\GraphQL\Controllers\List\Playlist\PlaylistTrackController;
use App\GraphQL\Definition\Queries\Models\Singular\EloquentSingularQuery;
use App\GraphQL\Definition\Types\List\Playlist\PlaylistTrackType;

#[UseBuilderDirective(PlaylistTrackController::class, 'show')]
class PlaylistTrackQuery extends EloquentSingularQuery
{
    public function __construct()
    {
        parent::__construct('playlisttrack');
    }

    /**
     * The description of the type.
     */
    public function description(): string
    {
        return 'Returns a playlist track resource.';
    }

    /**
     * The base return type of the query.
     */
    public function baseType(): PlaylistTrackType
    {
        return new PlaylistTrackType();
    }
}
