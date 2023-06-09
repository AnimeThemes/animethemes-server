<?php

declare(strict_types=1);

namespace App\Enums\Http\Api\Filter;

use App\Concerns\Enums\LocalizesName;

/**
 * Enum TrashedStatus.
 */
enum TrashedStatus: string
{
    use LocalizesName;

    case WITH = 'with';
    case WITHOUT = 'without';
    case ONLY = 'only';
}
