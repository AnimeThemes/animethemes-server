<?php

declare(strict_types=1);

namespace App\GraphQL\Definition\Queries\Wiki\Anime;

use App\GraphQL\Attributes\Resolvers\UsePaginateDirective;
use App\GraphQL\Attributes\UseSearchDirective;
use App\GraphQL\Definition\Queries\EloquentQuery;
use App\GraphQL\Definition\Types\Wiki\Anime\AnimeThemeType;

#[UsePaginateDirective]
#[UseSearchDirective]
class AnimeThemesQuery extends EloquentQuery
{
    public function __construct()
    {
        parent::__construct('animethemes');
    }

    /**
     * The description of the type.
     */
    public function description(): string
    {
        return 'Returns a listing of anime themes resources given fields.';
    }

    /**
     * The base return type of the query.
     */
    public function baseType(): AnimeThemeType
    {
        return new AnimeThemeType();
    }
}
