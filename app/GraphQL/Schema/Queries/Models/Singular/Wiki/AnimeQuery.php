<?php

declare(strict_types=1);

namespace App\GraphQL\Schema\Queries\Models\Singular\Wiki;

use App\GraphQL\Schema\Queries\Models\Singular\EloquentSingularQuery;
use App\GraphQL\Schema\Types\Wiki\AnimeType;

class AnimeQuery extends EloquentSingularQuery
{
    public function name(): string
    {
        return 'anime';
    }

    public function description(): string
    {
        return 'Returns an anime resource.';
    }

    /**
     * The base return type of the query.
     */
    public function baseType(): AnimeType
    {
        return new AnimeType();
    }
}
