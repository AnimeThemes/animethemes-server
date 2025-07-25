<?php

declare(strict_types=1);

namespace App\GraphQL\Definition\Queries\List;

use App\GraphQL\Attributes\Resolvers\UseBuilderDirective;
use App\GraphQL\Attributes\Resolvers\UsePaginateDirective;
use App\GraphQL\Attributes\UseSearchDirective;
use App\GraphQL\Builders\List\PlaylistBuilder;
use App\GraphQL\Definition\Queries\EloquentQuery;
use App\GraphQL\Definition\Types\List\PlaylistType;

#[UseBuilderDirective(PlaylistBuilder::class)]
#[UsePaginateDirective]
#[UseSearchDirective]
class PlaylistsQuery extends EloquentQuery
{
    public function __construct()
    {
        parent::__construct('playlists');
    }

    /**
     * The description of the type.
     */
    public function description(): string
    {
        return 'Returns a listing of playlist resources given fields.';
    }

    /**
     * The base return type of the query.
     */
    public function baseType(): PlaylistType
    {
        return new PlaylistType();
    }
}
