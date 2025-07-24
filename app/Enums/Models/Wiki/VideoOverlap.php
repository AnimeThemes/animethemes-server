<?php

declare(strict_types=1);

namespace App\Enums\Models\Wiki;

use App\Concerns\Enums\CoercesInstances;
use App\Concerns\Enums\LocalizesName;
use Filament\Support\Contracts\HasLabel;

enum VideoOverlap: int implements HasLabel
{
    use CoercesInstances;
    use LocalizesName;

    case NONE = 0;
    case TRANS = 1;
    case OVER = 2;
}
