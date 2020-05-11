<?php

namespace App\Enums;

use BenSampo\Enum\Enum;

final class SourceType extends Enum
{
    const WEB = 0;
    const RAW = 1;
    const BD  = 2;
    const DVD = 3;
    const VHS = 4;
}
