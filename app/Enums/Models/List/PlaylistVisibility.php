<?php

declare(strict_types=1);

namespace App\Enums\Models\List;

use App\Concerns\Enums\LocalizesName;

/**
 * Enum PlaylistVisibility.
 */
enum PlaylistVisibility: int
{
    use LocalizesName;

    case PUBLIC = 0;
    case PRIVATE = 1;
    case UNLISTED = 2;
}
