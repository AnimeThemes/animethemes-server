<?php

declare(strict_types=1);

namespace App\Enums\Models\List;

use App\Concerns\Enums\LocalizesName;
use Filament\Support\Contracts\HasLabel;

/**
 * Enum ExternalProfileVisibility.
 */
enum ExternalProfileVisibility: int implements HasLabel
{
    use LocalizesName;

    case PUBLIC = 0;
    case PRIVATE = 1;
}
