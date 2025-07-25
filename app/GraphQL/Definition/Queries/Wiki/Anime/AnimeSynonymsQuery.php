<?php

declare(strict_types=1);

namespace App\GraphQL\Definition\Queries\Wiki\Anime;

use App\GraphQL\Attributes\Resolvers\UsePaginateDirective;
use App\GraphQL\Definition\Queries\EloquentQuery;
use App\GraphQL\Definition\Types\Wiki\Anime\AnimeSynonymType;

#[UsePaginateDirective]
class AnimeSynonymsQuery extends EloquentQuery
{
    public function __construct()
    {
        parent::__construct('animesynonyms');
    }

    /**
     * The description of the type.
     */
    public function description(): string
    {
        return 'Returns a listing of anime synonyms resources given fields.';
    }

    /**
     * The base return type of the query.
     */
    public function baseType(): AnimeSynonymType
    {
        return new AnimeSynonymType();
    }
}
