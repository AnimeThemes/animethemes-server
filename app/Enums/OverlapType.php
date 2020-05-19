<?php

namespace App\Enums;

use BenSampo\Enum\Enum;
use BenSampo\Enum\Contracts\LocalizedEnum;

final class OverlapType extends Enum implements LocalizedEnum
{
    const NONE  = 0;
    const TRANS = 1;
    const OVER  = 2;
}
