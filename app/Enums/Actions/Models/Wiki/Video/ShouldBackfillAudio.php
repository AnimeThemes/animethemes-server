<?php

declare(strict_types=1);

namespace App\Enums\Actions\Models\Wiki\Video;

use App\Concerns\Enums\LocalizesName;

/**
 * Enum ShouldBackfillAudio.
 */
enum ShouldBackfillAudio: int
{
    use LocalizesName;

    case NO = 0;
    case YES = 1;

    /**
     * Get the field key to use in the admin panel.
     *
     * @return string
     */
    public static function getFieldKey(): string
    {
        return 'should-backfill-audio';
    }
}
