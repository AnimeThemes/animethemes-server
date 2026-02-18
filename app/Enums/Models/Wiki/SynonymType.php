<?php

declare(strict_types=1);

namespace App\Enums\Models\Wiki;

use App\Concerns\Enums\LocalizesName;
use Filament\Support\Contracts\HasLabel;

enum SynonymType: int implements HasLabel
{
    use LocalizesName;

    case OTHER = 0;
    case NATIVE = 1;
    case ENGLISH = 2;
    case SHORT = 3;
}
