<?php

declare(strict_types=1);

namespace App\GraphQL\Schema\Queries\Models\Pagination\Wiki\Anime;

use App\Contracts\GraphQL\Fields\DeprecatedField;
use App\GraphQL\Schema\Queries\Models\Pagination\EloquentPaginationQuery;
use App\GraphQL\Schema\Types\Wiki\Anime\AnimeSynonymType;

class AnimeSynonymPaginationQuery extends EloquentPaginationQuery implements DeprecatedField
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
    public function baseType(): AnimeSynonymType
    {
        return new AnimeSynonymType();
    }

    public function deprecationReason(): string
    {
        return 'Use synonymPagination query instead.';
    }
}
