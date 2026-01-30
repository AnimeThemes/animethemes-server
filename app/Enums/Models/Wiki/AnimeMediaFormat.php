<?php

declare(strict_types=1);

namespace App\Enums\Models\Wiki;

use App\Concerns\Enums\LocalizesName;
use Filament\Support\Contracts\HasLabel;

enum AnimeMediaFormat: int implements HasLabel
{
    use LocalizesName;

    case TV = 0;
    case TV_SHORT = 1;
    case OVA = 2;
    case MOVIE = 3;
    case SPECIAL = 4;
    case ONA = 5;
}
