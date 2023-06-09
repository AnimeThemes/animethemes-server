<?php

declare(strict_types=1);

namespace Tests\Unit\Enums;

use App\Concerns\Enums\LocalizesName;

/**
 * Enum LocalizedEnum.
 */
enum LocalizedEnum: int
{
    use LocalizesName;

    case ZERO = 0;
    case ONE = 1;
    case TWO = 2;
}
