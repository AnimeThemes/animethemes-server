<?php

declare(strict_types=1);

namespace App\Enums\Models\Wiki;

use App\Concerns\Enums\LocalizesName;
use Filament\Support\Contracts\HasLabel;

/**
 * Enum AnimeMediaFormat.
 */
enum AnimeMediaFormat: int implements HasLabel
{
    use LocalizesName;

    case UNKNOWN = 0;
    case TV = 1;
    case TV_SHORT = 2;
    case OVA = 3;
    case MOVIE = 4;
    case SPECIAL = 5;
    case ONA = 6;
}
