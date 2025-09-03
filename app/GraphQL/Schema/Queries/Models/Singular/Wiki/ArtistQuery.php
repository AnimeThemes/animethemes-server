<?php

declare(strict_types=1);

namespace App\GraphQL\Schema\Queries\Models\Singular\Wiki;

use App\GraphQL\Schema\Queries\Models\Singular\EloquentSingularQuery;
use App\GraphQL\Schema\Types\Wiki\ArtistType;

class ArtistQuery extends EloquentSingularQuery
{
    public function __construct()
    {
        parent::__construct('artist');
    }

    public function description(): string
    {
        return 'Returns an artist resource.';
    }

    /**
     * The base return type of the query.
     */
    public function baseRebingType(): ArtistType
    {
        return new ArtistType();
    }
}
