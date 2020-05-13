<?php

namespace App\Enums;

use BenSampo\Enum\Enum;
use BenSampo\Enum\Contracts\LocalizedEnum;

final class Season extends Enum implements LocalizedEnum
{
    const FALL   = 0;
    const SUMMER = 1;
    const SPRING = 2;
    const WINTER = 3;
}
