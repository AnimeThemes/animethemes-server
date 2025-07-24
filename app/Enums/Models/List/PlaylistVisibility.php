<?php

declare(strict_types=1);

namespace App\Enums\Models\List;

use App\Concerns\Enums\LocalizesName;
use Filament\Support\Contracts\HasLabel;

enum PlaylistVisibility: int implements HasLabel
{
    use LocalizesName;

    case PUBLIC = 0;
    case PRIVATE = 1;
    case UNLISTED = 2;
}
