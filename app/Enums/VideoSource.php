<?php

declare(strict_types=1);

namespace App\Enums;

use BenSampo\Enum\Contracts\LocalizedEnum;
use BenSampo\Enum\Enum;

/**
 * Class VideoSource.
 */
final class VideoSource extends Enum implements LocalizedEnum
{
    public const WEB = 0;
    public const RAW = 1;
    public const BD = 2;
    public const DVD = 3;
    public const VHS = 4;
    public const LD = 5;
}
