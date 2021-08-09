<?php

declare(strict_types=1);

namespace App\Enums\Models\Wiki;

use App\Enums\BaseEnum;

/**
 * Class VideoSource.
 */
final class VideoSource extends BaseEnum
{
    public const WEB = 0;
    public const RAW = 1;
    public const BD = 2;
    public const DVD = 3;
    public const VHS = 4;
    public const LD = 5;
}
