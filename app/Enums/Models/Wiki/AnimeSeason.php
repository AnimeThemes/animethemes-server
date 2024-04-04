<?php

declare(strict_types=1);

namespace App\Enums\Models\Wiki;

use App\Concerns\Enums\LocalizesName;
use Filament\Support\Contracts\HasLabel;

/**
 * Enum AnimeSeason.
 */
enum AnimeSeason: int implements HasLabel
{
    use LocalizesName;

    case WINTER = 0;
    case SPRING = 1;
    case SUMMER = 2;
    case FALL = 3;

    public function getLabel(): ?string
    {
        return $this->localize();
    }
}
