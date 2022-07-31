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

    /**
     * Score sources to help prioritize videos.
     *
     * @param  int|null  $value
     * @return int
     */
    public static function getPriority(?int $value): int
    {
        return match ($value) {
            self::BD => 60,
            self::DVD => 50,
            self::LD => 40,
            self::VHS => 30,
            self::WEB => 20,
            self::RAW => 10,
            default => 0,
        };
    }
}
