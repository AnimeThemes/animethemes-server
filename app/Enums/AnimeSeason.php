<?php

namespace App\Enums;

use BenSampo\Enum\Contracts\LocalizedEnum;
use BenSampo\Enum\Enum;

final class AnimeSeason extends Enum implements LocalizedEnum
{
    const WINTER = 0;
    const SPRING = 1;
    const SUMMER = 2;
    const FALL = 3;
}
