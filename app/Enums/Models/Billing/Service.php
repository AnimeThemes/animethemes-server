<?php

declare(strict_types=1);

namespace App\Enums\Models\Billing;

use App\Concerns\Enums\CoercesInstances;
use App\Concerns\Enums\LocalizesName;

/**
 * Enum Service.
 */
enum Service: int
{
    use CoercesInstances;
    use LocalizesName;

    case OTHER = 0;
    case DIGITALOCEAN = 1;
    case AWS = 2;
    case HOVER = 3;
    case WALKERSERVERS = 4;
}
