<?php

declare(strict_types=1);

namespace App\GraphQL\Schema\Queries\Models\Pagination\Wiki\Anime\Theme;

use App\GraphQL\Schema\Queries\Models\Pagination\EloquentPaginationQuery;
use App\GraphQL\Schema\Types\Wiki\Anime\Theme\AnimeThemeEntryType;

class AnimeThemeEntryPaginationQuery extends EloquentPaginationQuery
{
    public function __construct()
    {
        parent::__construct('animethemeentryPagination');
    }

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
