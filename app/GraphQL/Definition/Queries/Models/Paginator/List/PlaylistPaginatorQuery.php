<?php

declare(strict_types=1);

namespace App\GraphQL\Definition\Queries\Models\Paginator\List;

use App\GraphQL\Attributes\Resolvers\UseBuilderDirective;
use App\GraphQL\Attributes\Resolvers\UsePaginateDirective;
use App\GraphQL\Attributes\UseSearchDirective;
use App\GraphQL\Controllers\List\PlaylistController;
use App\GraphQL\Definition\Queries\Models\Paginator\EloquentPaginatorQuery;
use App\GraphQL\Definition\Types\List\PlaylistType;

#[UseBuilderDirective(PlaylistController::class)]
#[UsePaginateDirective]
#[UseSearchDirective]
class PlaylistPaginatorQuery extends EloquentPaginatorQuery
{
    public function __construct()
    {
        parent::__construct('playlistPaginator');
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
