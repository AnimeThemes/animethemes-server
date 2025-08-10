<?php

declare(strict_types=1);

namespace App\GraphQL\Definition\Queries\Models\Paginator\Wiki;

use App\GraphQL\Definition\Queries\Models\Paginator\EloquentPaginatorQuery;
use App\GraphQL\Definition\Types\Wiki\SongType;

class SongPaginatorQuery extends EloquentPaginatorQuery
{
    public function __construct()
    {
        parent::__construct('songPaginator');
    }

    /**
     * The description of the type.
     */
    public function description(): string
    {
        return 'Returns a listing of song resources given fields.';
    }

    /**
     * The base return type of the query.
     */
    public function baseRebingType(): SongType
    {
        return new SongType();
    }
}
