<?php

declare(strict_types=1);

namespace App\GraphQL\Definition\Queries\Models\Singular\Wiki;

use App\GraphQL\Definition\Queries\Models\Singular\EloquentSingularQuery;
use App\GraphQL\Definition\Types\Wiki\StudioType;

class StudioQuery extends EloquentSingularQuery
{
    public function __construct()
    {
        parent::__construct('studio');
    }

    public function description(): string
    {
        return 'Returns a studio resource.';
    }

    /**
     * The base return type of the query.
     */
    public function baseRebingType(): StudioType
    {
        return new StudioType();
    }
}
