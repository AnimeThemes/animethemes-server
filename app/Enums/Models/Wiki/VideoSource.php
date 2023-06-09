<?php

declare(strict_types=1);

namespace App\Enums\Models\Wiki;

use App\Concerns\Enums\CoercesInstances;
use App\Concerns\Enums\LocalizesName;

/**
 * Enum VideoSource.
 */
enum VideoSource: int
{
    use CoercesInstances;
    use LocalizesName;

    case WEB = 0;
    case RAW = 1;
    case BD = 2;
    case DVD = 3;
    case VHS = 4;
    case LD = 5;

    /**
     * Score sources to help prioritize videos.
     * Note: This should be refactored into attributes.
     *
     * @return int
     */
    public function getPriority(): int
    {
        return match ($this) {
            self::BD => 60,
            self::DVD => 50,
            self::LD => 40,
            self::VHS => 30,
            self::WEB => 20,
            self::RAW => 10,
        };
    }
}
