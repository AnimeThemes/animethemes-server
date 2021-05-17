<?php

namespace App\Enums;

use BenSampo\Enum\Enum;

final class BillingFrequency extends Enum
{
    const ONCE = 0;
    const ANNUALLY = 1;
    const BIANNUALLY = 2;
    const QUARTERLY = 3;
    const MONTHLY = 4;
}
