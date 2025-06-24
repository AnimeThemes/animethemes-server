<?php

declare(strict_types=1);

namespace App\Enums\Actions\Models\Wiki\Video;

use App\Concerns\Enums\LocalizesName;
use Filament\Support\Contracts\HasLabel;

/**
 * Enum DiscordNotificationType.
 */
enum DiscordNotificationType: string implements HasLabel
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
        return 'discord-notification-type';
    }
}
