<?php

declare(strict_types=1);

namespace App\GraphQL\Definition\Queries\List;

use App\GraphQL\Builders\List\PlaylistBuilder;
use App\GraphQL\Definition\Queries\EloquentQuery;
use App\GraphQL\Definition\Types\List\PlaylistType;

/**
 * Class PlaylistsQuery.
 */
class PlaylistsQuery extends EloquentQuery
{
    public function __construct()
    {
        parent::__construct('playlists');
    }

    /**
     * The description of the type.
     *
     * @return string
     */
    public function description(): string
    {
        return 'Returns a listing of playlist resources given fields.';
    }

    /**
     * The directives of the type.
     *
     * @return array<string, array>
     */
    public function directives(): array
    {
        return [
            'builder' => [
                'method' => PlaylistBuilder::class.'@index',
            ],

            ...parent::directives(),
        ];
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

            'orderBy: _ @orderBy(columnsEnum: "PlaylistColumnsOrderable",
                relations: [
                    {relation: "likeAggregate", columns: ["value"]},
                    {relation: "viewAggregate", columns: ["value"]}
                ]
            )',
        ];
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
