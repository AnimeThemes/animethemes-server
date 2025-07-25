<?php

declare(strict_types=1);

namespace App\GraphQL\Definition\Queries\Wiki;

use App\GraphQL\Attributes\Resolvers\UsePaginateDirective;
use App\GraphQL\Attributes\UseSearchDirective;
use App\GraphQL\Definition\Queries\EloquentQuery;
use App\GraphQL\Definition\Types\Wiki\ArtistType;

#[UsePaginateDirective]
#[UseSearchDirective]
class ArtistsQuery extends EloquentQuery
{
    public function __construct()
    {
        parent::__construct('artists');
    }

    /**
     * The description of the type.
     */
    public function description(): string
    {
        return 'Returns a listing of artist resources given fields.';
    }

    /**
     * The base return type of the query.
     */
    public function baseType(): ArtistType
    {
        return new ArtistType();
    }
}
