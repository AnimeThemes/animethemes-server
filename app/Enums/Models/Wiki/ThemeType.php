<?php

declare(strict_types=1);

namespace App\Enums\Models\Wiki;

use App\Concerns\Enums\LocalizesName;
use App\GraphQL\Types\Definition\Hidden;

/**
 * Enum ThemeType.
 */
enum ThemeType: int
{
    use LocalizesName;

    case OP = 0;

    case ED = 1;

    #[Hidden(true)]
    case IN = 2;
}
