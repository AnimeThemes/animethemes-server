<?php

namespace App\Enums;

use BenSampo\Enum\Enum;
use BenSampo\Enum\Contracts\LocalizedEnum;

final class ThemeType extends Enum implements LocalizedEnum
{
    const OP = 0;
    const ED = 1;
}
