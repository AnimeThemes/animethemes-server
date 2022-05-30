<?php

declare(strict_types=1);

namespace App\Enums\Models\Billing;

use App\Enums\BaseEnum;

/**
 * Class BalanceFrequency.
 */
final class BalanceFrequency extends BaseEnum
{
    public const ONCE = 0;
    public const ANNUALLY = 1;
    public const BIANNUALLY = 2;
    public const QUARTERLY = 3;
    public const MONTHLY = 4;
}
