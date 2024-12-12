<?php

declare(strict_types=1);

namespace App\Enums\Actions\Models\Wiki\Video;

use App\Concerns\Enums\LocalizesName;

/**
 * Enum NotificationType.
 */
enum NotificationType: string
{
    use LocalizesName;

    case ADDED = 'added';
    case UPDATED = 'updated';

    /**
     * Get the field key to use in the admin panel.
     *
     * @return string
     */
    public static function getFieldKey(): string
    {
        return 'notification-type';
    }
}
