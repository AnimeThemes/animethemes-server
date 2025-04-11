<?php

declare(strict_types=1);

namespace App\Enums\Models\User;

use App\Concerns\Enums\LocalizesName;

/**
 * Enum ReportActionType.
 */
enum ReportActionType: int
{
    use LocalizesName;

    case CREATE = 0;
    case UPDATE = 1;
    case DELETE = 2;
    case ATTACH = 3;
    case DETACH = 4;
}
