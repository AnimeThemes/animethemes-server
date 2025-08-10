<?php

declare(strict_types=1);

namespace App\GraphQL\Definition\Queries\Models\Paginator\Wiki;

use App\GraphQL\Definition\Queries\Models\Paginator\EloquentPaginatorQuery;
use App\GraphQL\Definition\Types\Wiki\ArtistType;

class ArtistPaginatorQuery extends EloquentPaginatorQuery
{
    public function __construct()
    {
        parent::__construct('artistPaginator');
    }

    /**
     * The description of the type.
     */
    public function description(): string
    {
        return 'Returns a listing of artist resources given fields.';
    }

    /**
     * The base return type of the query.
     */
    public function baseRebingType(): ArtistType
    {
        return new ArtistType();
    }
}
