<?php

declare(strict_types=1);

namespace App\Enums\Models\Wiki;

use App\Concerns\Enums\LocalizesName;
use App\GraphQL\Attributes\Hidden;
use Filament\Support\Contracts\HasLabel;

enum ThemeType: int implements HasLabel
{
    use LocalizesName;

    case OP = 0;

    case ED = 1;

    #[Hidden]
    case IN = 2;
}
