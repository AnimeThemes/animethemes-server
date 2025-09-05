<?php

declare(strict_types=1);

namespace App\GraphQL\Schema\Queries\Models\Singular\Wiki;

use App\GraphQL\Schema\Queries\Models\Singular\EloquentSingularQuery;
use App\GraphQL\Schema\Types\Wiki\StudioType;

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
    public function baseType(): StudioType
    {
        return new StudioType();
    }
}
