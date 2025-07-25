<?php

declare(strict_types=1);

namespace App\GraphQL\Definition\Queries\List;

use App\GraphQL\Attributes\UseBuilder;
use App\GraphQL\Builders\List\PlaylistBuilder;
use App\GraphQL\Definition\Queries\EloquentQuery;
use App\GraphQL\Definition\Types\List\PlaylistType;

#[UseBuilder(PlaylistBuilder::class)]
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
     * The arguments of the type.
     *
     * @return string[]
     */
    public function arguments(): array
    {
        return [
            'search: String @search',

            ...parent::arguments(),
        ];
    }

    /**
     * The base return type of the query.
     */
    public function baseType(): PlaylistType
    {
        return new PlaylistType();
    }
}
