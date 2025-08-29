<?php

declare(strict_types=1);

namespace App\GraphQL\Definition\Queries\Models\Singular\Wiki;

use App\GraphQL\Definition\Queries\Models\Singular\EloquentSingularQuery;
use App\GraphQL\Definition\Types\Wiki\AnimeType;

class AnimeQuery extends EloquentSingularQuery
{
    public function __construct()
    {
        parent::__construct('anime');
    }

    public function description(): string
    {
        return 'Returns an anime resource.';
    }

    /**
     * The base return type of the query.
     */
    public function baseRebingType(): AnimeType
    {
        return new AnimeType();
    }
}
