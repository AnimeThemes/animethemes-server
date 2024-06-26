<?php

declare(strict_types=1);

namespace App\Enums\Models\List;

use App\Concerns\Enums\LocalizesName;

/**
 * Enum AnimeWatchStatus.
 */
enum AnimeWatchStatus: int
{
    use LocalizesName;

    case WATCHING = 0;
    case COMPLETED = 1;
    case PAUSED = 2;
    case DROPPED = 3;
    case PLAN_TO_WATCH = 4;
}
