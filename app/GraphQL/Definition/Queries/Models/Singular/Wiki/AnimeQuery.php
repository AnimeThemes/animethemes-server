<?php

declare(strict_types=1);

namespace App\GraphQL\Definition\Queries\Models\Singular\Wiki;

use App\GraphQL\Attributes\Resolvers\UseBuilderDirective;
use App\GraphQL\Controllers\Wiki\AnimeController;
use App\GraphQL\Definition\Queries\Models\Singular\EloquentSingularQuery;
use App\GraphQL\Definition\Types\Wiki\AnimeType;

#[UseBuilderDirective(AnimeController::class, 'show')]
class AnimeQuery extends EloquentSingularQuery
{
    public function __construct()
    {
        parent::__construct('anime');
    }

    /**
     * The description of the type.
     */
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
