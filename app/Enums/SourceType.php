<?php

namespace App\Enums;

use BenSampo\Enum\Enum;
use BenSampo\Enum\Contracts\LocalizedEnum;

final class SourceType extends Enum implements LocalizedEnum
{
    const WEB = 0;
    const RAW = 1;
    const BD  = 2;
    const DVD = 3;
    const VHS = 4;
}
