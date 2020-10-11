<?php

namespace App\Enums;

use BenSampo\Enum\Contracts\LocalizedEnum;
use BenSampo\Enum\Enum;

final class ThemeType extends Enum implements LocalizedEnum
{
    const OP = 0;
    const ED = 1;
}
