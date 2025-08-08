<?php

declare(strict_types=1);

namespace App\GraphQL\Definition\Queries\Models\Singular\Wiki;

use App\GraphQL\Attributes\Resolvers\UseBuilderDirective;
use App\GraphQL\Controllers\Wiki\StudioController;
use App\GraphQL\Definition\Queries\Models\Singular\EloquentSingularQuery;
use App\GraphQL\Definition\Types\Wiki\StudioType;

#[UseBuilderDirective(StudioController::class, 'show')]
class StudioQuery extends EloquentSingularQuery
{
    public function __construct()
    {
        parent::__construct('studio');
    }

    /**
     * The description of the type.
     */
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
