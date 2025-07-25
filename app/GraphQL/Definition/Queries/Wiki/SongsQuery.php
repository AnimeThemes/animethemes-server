<?php

declare(strict_types=1);

namespace App\GraphQL\Definition\Queries\Wiki;

use App\GraphQL\Definition\Queries\EloquentQuery;
use App\GraphQL\Definition\Types\Wiki\SongType;

class SongsQuery extends EloquentQuery
{
    public function __construct()
    {
        parent::__construct('songs');
    }

    /**
     * The description of the type.
     */
    public function description(): string
    {
        return 'Returns a listing of song resources given fields.';
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
    public function baseType(): SongType
    {
        return new SongType();
    }
}
