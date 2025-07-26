<?php

declare(strict_types=1);

namespace App\GraphQL\Definition\Queries\List\Playlist;

use App\GraphQL\Attributes\Resolvers\UseBuilderDirective;
use App\GraphQL\Attributes\Resolvers\UseFindDirective;
use App\GraphQL\Builders\List\Playlist\PlaylistTrackBuilder;
use App\GraphQL\Definition\Queries\EloquentSingularQuery;
use App\GraphQL\Definition\Types\List\Playlist\PlaylistTrackType;

#[UseBuilderDirective(PlaylistTrackBuilder::class, 'show')]
#[UseFindDirective]
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
