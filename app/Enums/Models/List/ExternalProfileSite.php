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
     * @return ResourceSite
     */
    public function getResourceSite(): ResourceSite
    {
        return match ($this) {
            static::MAL => ResourceSite::MAL,
            static::ANILIST => ResourceSite::ANILIST,
            static::KITSU => ResourceSite::KITSU,
        };
    }
}
