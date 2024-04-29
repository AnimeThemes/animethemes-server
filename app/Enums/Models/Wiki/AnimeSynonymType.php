<?php

declare(strict_types=1);

namespace App\Enums\Models\Wiki;

use App\Concerns\Enums\LocalizesName;

/**
 * Enum AnimeSynonymType.
 */
enum AnimeSynonymType: int
{
    use LocalizesName;

    case OTHER = 0;
    case NATIVE = 1;
    case ENGLISH = 2;
    case SHORT = 3;
}
