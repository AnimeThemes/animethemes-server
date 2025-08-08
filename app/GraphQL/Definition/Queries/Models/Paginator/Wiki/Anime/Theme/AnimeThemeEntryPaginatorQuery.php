<?php

declare(strict_types=1);

namespace App\GraphQL\Definition\Queries\Models\Paginator\Wiki\Anime\Theme;

use App\GraphQL\Attributes\Resolvers\UsePaginateDirective;
use App\GraphQL\Attributes\UseSearchDirective;
use App\GraphQL\Definition\Queries\Models\Paginator\EloquentPaginatorQuery;
use App\GraphQL\Definition\Types\Wiki\Anime\Theme\AnimeThemeEntryType;

#[UsePaginateDirective]
#[UseSearchDirective]
class AnimeThemeEntryPaginatorQuery extends EloquentPaginatorQuery
{
    public function __construct()
    {
        parent::__construct('animethemeentryPaginator');
    }

    /**
     * The description of the type.
     */
    public function description(): string
    {
        return 'Returns a listing of anime theme entries resources given fields.';
    }

    /**
     * The base return type of the query.
     */
    public function baseType(): AnimeThemeEntryType
    {
        return new AnimeThemeEntryType();
    }
}
