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
            self::WINTER => '153, 204, 255',
            self::SPRING => '255, 192, 203',
            self::SUMMER => '255, 153, 51',
            self::FALL => '204, 102, 0',
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
            self::WINTER => "\u{2744}",
            self::SPRING => "\u{1F33C}",
            self::SUMMER => "\u{2600}",
            self::FALL => "\u{1F342}",
        };
    }
}
