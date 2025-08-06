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

    #[Description("Insert Song\n\nNote: Not retrieved by default, include it in the type_in argument to do so.")]
    case IN = 2;
}
