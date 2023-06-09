<?php

declare(strict_types=1);

namespace App\Enums\Models\Wiki;

use App\Concerns\Enums\CoercesInstances;
use App\Concerns\Enums\LocalizesName;

/**
 * Enum VideoOverlap.
 */
enum VideoOverlap: int
{
    use CoercesInstances;
    use LocalizesName;

    case NONE = 0;
    case TRANS = 1;
    case OVER = 2;
}
