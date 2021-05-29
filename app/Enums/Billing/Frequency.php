<?php

namespace App\Enums\Billing;

use BenSampo\Enum\Enum;

final class Frequency extends Enum
{
    const ONCE = 0;
    const ANNUALLY = 1;
    const BIANNUALLY = 2;
    const QUARTERLY = 3;
    const MONTHLY = 4;
}
