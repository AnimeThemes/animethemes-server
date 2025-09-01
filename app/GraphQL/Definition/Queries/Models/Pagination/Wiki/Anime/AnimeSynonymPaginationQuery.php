<?php

declare(strict_types=1);

namespace App\GraphQL\Definition\Queries\Models\Pagination\Wiki\Anime;

use App\GraphQL\Definition\Queries\Models\Pagination\EloquentPaginationQuery;
use App\GraphQL\Definition\Types\Wiki\Anime\AnimeSynonymType;

class AnimeSynonymPaginationQuery extends EloquentPaginationQuery
{
    public function __construct()
    {
        parent::__construct('animesynonymPagination');
    }

    public function description(): string
    {
        return 'Returns a listing of anime synonyms resources given fields.';
    }

    /**
     * The base return type of the query.
     */
    public function baseRebingType(): AnimeSynonymType
    {
        return new AnimeSynonymType();
    }
}
