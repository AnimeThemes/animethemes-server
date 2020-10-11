<?php

namespace App\Enums;

use BenSampo\Enum\Contracts\LocalizedEnum;
use BenSampo\Enum\Enum;

final class OverlapType extends Enum implements LocalizedEnum
{
    const NONE = 0;
    const TRANS = 1;
    const OVER = 2;
}
