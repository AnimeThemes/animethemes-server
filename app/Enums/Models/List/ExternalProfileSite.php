<?php

declare(strict_types=1);

namespace App\Enums\Models\List;

use App\Concerns\Enums\LocalizesName;
use App\Enums\Models\Wiki\ResourceSite;

/**
 * Enum ExternalProfileSite.
 */
enum ExternalProfileSite: int
{
    use LocalizesName;

    case MAL = 0;
    case ANILIST = 1;
    case KITSU = 2;

    /**
     * Get the ResourceSite by the ExternalProfileSite value.
     *
     * @param  int|null  $value
     * @return ResourceSite|null
     */
    public static function getResourceSite(?int $value): ?ResourceSite
    {
        return match ($value) {
            static::MAL->value => ResourceSite::MAL,
            static::ANILIST->value => ResourceSite::ANILIST,
            static::KITSU->value => ResourceSite::KITSU,
            default => null,
        };
    }
}
