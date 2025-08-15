<?php

declare(strict_types=1);

namespace App\GraphQL\Definition\Queries\Models\Singular\Wiki;

use App\GraphQL\Definition\Queries\Models\Singular\EloquentSingularQuery;
use App\GraphQL\Definition\Types\Wiki\SeriesType;

class SeriesQuery extends EloquentSingularQuery
{
    public function __construct()
    {
        parent::__construct('series');
    }

    /**
     * The description of the type.
     */
    public function description(): string
    {
        return 'Returns a series resource.';
    }

    /**
     * The base return type of the query.
     */
    public function baseRebingType(): SeriesType
    {
        return new SeriesType();
    }
}
