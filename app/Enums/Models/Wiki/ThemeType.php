<?php

declare(strict_types=1);

namespace App\Enums\Models\Wiki;

use App\Concerns\Enums\LocalizesName;

/**
 * Enum ThemeType.
 */
enum ThemeType: int
{
    use LocalizesName;

    case OP = 0;
    case ED = 1;
    case IS = 2;
}
