<?php

declare(strict_types=1);

namespace App\Enums\Pivots\Document;

use App\Concerns\Enums\LocalizesName;
use Filament\Support\Contracts\HasLabel;

enum PageRoleType: int implements HasLabel
{
    use LocalizesName;

    case VIEWER = 0;
    case EDITOR = 1;
}
