<?php

declare(strict_types=1);

namespace App\Enums\Models\List;

use App\Concerns\Enums\LocalizesName;
use Filament\Support\Contracts\HasLabel;

enum ExternalEntryStatus: int implements HasLabel
{
    use LocalizesName;

    case WATCHING = 0;
    case COMPLETED = 1;
    case PAUSED = 2;
    case DROPPED = 3;
    case PLAN_TO_WATCH = 4;
    case REWATCHING = 5;

    /**
     * Get the status by the MAL key.
     */
    public static function getMalMapping(string $status): static
    {
        return match ($status) {
            'watching' => self::WATCHING,
            'completed' => self::COMPLETED,
            'on_hold' => self::PAUSED,
            'dropped' => self::DROPPED,
            'plan_to_watch' => self::PLAN_TO_WATCH,
            default => self::REWATCHING,
        };
    }

    /**
     * Get the status by the AniList key.
     */
    public static function getAnilistMapping(string $status): static
    {
        return match ($status) {
            'CURRENT' => self::WATCHING,
            'COMPLETED' => self::COMPLETED,
            'DROPPED' => self::DROPPED,
            'PAUSED' => self::PAUSED,
            'REPEATING' => self::REWATCHING,
            default => self::PLAN_TO_WATCH,
        };
    }
}
