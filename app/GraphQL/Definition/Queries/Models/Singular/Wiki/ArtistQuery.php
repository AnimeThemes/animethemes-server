<?php

declare(strict_types=1);

namespace App\GraphQL\Definition\Queries\Models\Singular\Wiki;

use App\GraphQL\Attributes\Resolvers\UseBuilderDirective;
use App\GraphQL\Controllers\Wiki\ArtistController;
use App\GraphQL\Definition\Queries\Models\Singular\EloquentSingularQuery;
use App\GraphQL\Definition\Types\Wiki\ArtistType;

#[UseBuilderDirective(ArtistController::class, 'show')]
class ArtistQuery extends EloquentSingularQuery
{
    public function __construct()
    {
        parent::__construct('artist');
    }

    /**
     * The description of the type.
     */
    public function description(): string
    {
        return 'Returns an artist resource.';
    }

    /**
     * The base return type of the query.
     */
    public function baseType(): ArtistType
    {
        return new ArtistType();
    }
}
