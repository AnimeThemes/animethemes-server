<?php

declare(strict_types=1);

namespace App\Enums\Models\User;

use App\Models\User\Notification;
use App\Notifications\ExternalProfileSyncedNotification;

enum NotificationType: int
{
    case PROFILE_SYNCED = 0;

    public static function resolveType(Notification $notification): ?NotificationType
    {
        return match ($notification->getAttribute(Notification::ATTRIBUTE_TYPE)) {
            ExternalProfileSyncedNotification::class => self::PROFILE_SYNCED,
            default => null,
        };
    }
}
