<?php

declare(strict_types=1);

namespace App\GraphQL\Schema\Queries\Models\Pagination\Wiki\Anime\Theme;

use App\Contracts\GraphQL\Fields\DeprecatedField;
use App\GraphQL\Schema\Queries\Models\Pagination\EloquentPaginationQuery;
use App\GraphQL\Schema\Types\Wiki\Anime\Theme\AnimeThemeEntryType;

class AnimeThemeEntryPaginationQuery extends EloquentPaginationQuery implements DeprecatedField
{
    public function name(): string
    {
        return 'animethemeentryPagination';
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

    public function deprecationReason(): string
    {
        return 'Internal use only';
    }
}
