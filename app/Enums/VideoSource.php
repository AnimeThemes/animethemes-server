<?php

namespace App\Enums;

use BenSampo\Enum\Contracts\LocalizedEnum;
use BenSampo\Enum\Enum;

final class VideoSource extends Enum implements LocalizedEnum
{
    const WEB = 0;
    const RAW = 1;
    const BD = 2;
    const DVD = 3;
    const VHS = 4;
    const LD = 5;
}
