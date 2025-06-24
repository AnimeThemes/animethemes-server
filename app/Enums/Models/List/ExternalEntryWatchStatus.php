<?php

declare(strict_types=1);

namespace App\Enums\Models\List;

use App\Concerns\Enums\LocalizesName;
use Filament\Support\Contracts\HasLabel;

/**
 * Enum ExternalEntryWatchStatus.
 */
enum ExternalEntryWatchStatus: int implements HasLabel
{
    use LocalizesName;

    case WATCHING = 0;
    case COMPLETED = 1;
    case PAUSED = 2;
    case DROPPED = 3;
    case PLAN_TO_WATCH = 4;
    case REWATCHING = 5;

    /**
     * Get the watch status by the MAL key.
     *
     * @param  string  $status
     * @return static
     */
    public static function getMalMapping(string $status): static
    {
        return match ($status) {
            'watching' => static::WATCHING,
            'completed' => static::COMPLETED,
            'on_hold' => static::PAUSED,
            'dropped' => static::DROPPED,
            'plan_to_watch' => static::PLAN_TO_WATCH,
            default => static::REWATCHING,
        };
    }

    /**
     * Get the watch status by the AniList key.
     *
     * @param  string  $status
     * @return static
     */
    public static function getAnilistMapping(string $status): static
    {
        return match ($status) {
            'CURRENT' => static::WATCHING,
            'COMPLETED' => static::COMPLETED,
            'DROPPED' => static::DROPPED,
            'PAUSED' => static::PAUSED,
            'REPEATING' => static::REWATCHING,
            default => static::PLAN_TO_WATCH,
        };
    }
}
