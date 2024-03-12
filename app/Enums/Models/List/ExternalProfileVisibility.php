<?php

declare(strict_types=1);

namespace App\Enums\Models\List;

use App\Concerns\Enums\LocalizesName;

/**
 * Enum ExternalProfileVisibility.
 */
enum ExternalProfileVisibility: int
{
    use LocalizesName;

    case PUBLIC = 0;
    case PRIVATE = 1;
}
