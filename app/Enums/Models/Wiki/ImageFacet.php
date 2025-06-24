<?php

declare(strict_types=1);

namespace App\Enums\Models\Wiki;

use App\Concerns\Enums\LocalizesName;
use Filament\Support\Contracts\HasLabel;

/**
 * Enum ImageFacet.
 */
enum ImageFacet: int implements HasLabel
{
    use LocalizesName;

    case SMALL_COVER = 0;
    case LARGE_COVER = 1;
    case GRILL = 2;
    case DOCUMENT = 3;
    case AVATAR = 4;
    case BANNER = 5;
}
