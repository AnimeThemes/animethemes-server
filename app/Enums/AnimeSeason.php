<?php

namespace App\Enums;

use BenSampo\Enum\Contracts\LocalizedEnum;
use BenSampo\Enum\Enum;

final class AnimeSeason extends Enum implements LocalizedEnum
{
    const FALL = 0;
    const SUMMER = 1;
    const SPRING = 2;
    const WINTER = 3;
}
