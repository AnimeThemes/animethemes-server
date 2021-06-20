<?php

declare(strict_types=1);

namespace App\Enums\Models\Billing;

use BenSampo\Enum\Enum;

/**
 * Class BalanceFrequency.
 */
final class BalanceFrequency extends Enum
{
    public const ONCE = 0;
    public const ANNUALLY = 1;
    public const BIANNUALLY = 2;
    public const QUARTERLY = 3;
    public const MONTHLY = 4;
}
