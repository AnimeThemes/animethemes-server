<?php

declare(strict_types=1);

namespace App\Enums\Models\Wiki;

use App\Concerns\Enums\LocalizesName;

/**
 * Enum ImageFacet.
 */
enum ImageFacet: int
{
    use LocalizesName;

    case COVER_SMALL = 0;
    case COVER_LARGE = 1;
    case GRILL = 2;
    case DOCUMENT = 3;
    case AVATAR = 4;
    case BANNER = 5;
}
