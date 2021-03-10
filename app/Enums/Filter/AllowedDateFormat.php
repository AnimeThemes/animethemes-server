<?php

namespace App\Enums\Filter;

use BenSampo\Enum\Enum;

final class AllowedDateFormat extends Enum
{
    const WITH_MICRO = 'Y-m-d\TH:i:s.u';
    const WITH_SEC = 'Y-m-d\TH:i:s';
    const WITH_MIN = 'Y-m-d\TH:i';
    const WITH_HOUR = 'Y-m-d\TH';
    const WITH_DAY = 'Y-m-d';
    const WITH_MONTH = 'Y-m';
    const WITH_YEAR = 'Y';
}
