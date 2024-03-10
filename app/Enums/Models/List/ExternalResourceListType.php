<?php

declare(strict_types=1);

namespace App\Enums\Models\List;

use App\Concerns\Enums\LocalizesName;

/**
 * Enum ExternalResourceListType.
 */
enum ExternalResourceListType: int
{
    use LocalizesName;

    case MAL = 0;
    case ANILIST = 1;
    case KITSU = 2;
}
