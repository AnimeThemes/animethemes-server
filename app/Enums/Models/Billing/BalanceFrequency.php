<?php

declare(strict_types=1);

namespace App\Enums\Models\Billing;

use App\Concerns\Enums\LocalizesName;

/**
 * Enum BalanceFrequency.
 */
enum BalanceFrequency: int
{
    use LocalizesName;

    case ONCE = 0;
    case ANNUALLY = 1;
    case BIANNUALLY = 2;
    case QUARTERLY = 3;
    case MONTHLY = 4;
}
