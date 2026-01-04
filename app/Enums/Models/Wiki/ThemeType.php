<?php

declare(strict_types=1);

namespace App\Enums\Models\Wiki;

use App\Concerns\Enums\LocalizesName;
use Filament\Support\Contracts\HasLabel;
use GraphQL\Type\Definition\Description;

enum ThemeType: int implements HasLabel
{
    use LocalizesName;

    #[Description('Opening')]
    case OP = 0;

    #[Description('Ending')]
    case ED = 1;

    #[Description('Insert Song')]
    case IN = 2;
}
