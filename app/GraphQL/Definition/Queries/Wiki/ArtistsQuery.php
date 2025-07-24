<?php

declare(strict_types=1);

namespace App\GraphQL\Definition\Queries\Wiki;

use App\GraphQL\Definition\Queries\EloquentQuery;
use App\GraphQL\Definition\Types\Wiki\ArtistType;

class ArtistsQuery extends EloquentQuery
{
    public function __construct()
    {
        parent::__construct('artists');
    }

    /**
     * The description of the type.
     */
    public function description(): string
    {
        return 'Returns a listing of artist resources given fields.';
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

            'orderBy: _ @orderBy(columnsEnum: "ArtistColumnsOrderable")',
        ];
    }

    /**
     * The base return type of the query.
     */
    public function baseType(): ArtistType
    {
        return new ArtistType();
    }
}
