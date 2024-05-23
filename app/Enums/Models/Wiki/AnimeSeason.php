<?php

declare(strict_types=1);

namespace App\Enums\Models\Wiki;

use App\Concerns\Enums\LocalizesName;
use App\Concerns\Filament\Enums\HasColorOrEmoji;

/**
 * Enum AnimeSeason.
 */
enum AnimeSeason: int
{
    use HasColorOrEmoji;
    use LocalizesName;

    case WINTER = 0;
    case SPRING = 1;
    case SUMMER = 2;
    case FALL = 3;

    /**
     * Get the rgb color for the enum.
     *
     * @return string
     */
    public function getColor(): string
    {
        return match ($this) {
            static::WINTER => '153, 204, 255',
            static::SPRING => '255, 192, 203',
            static::SUMMER => '255, 153, 51',
            static::FALL => '204, 102, 0',
        };
    }

    /**
     * Get the unicode emoji for the enum.
     *
     * @return string
     */
    public function getEmoji(): string
    {
        return match ($this) {
            static::WINTER => "\u{2744}",
            static::SPRING => "\u{1F33C}",
            static::SUMMER => "\u{2600}",
            static::FALL => "\u{1F342}",
        };
    }
}
