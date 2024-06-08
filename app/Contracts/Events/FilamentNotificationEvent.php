<?php

declare(strict_types=1);

namespace App\Contracts\Events;

use Filament\Notifications\Notification;
use Illuminate\Support\Collection;

/**
 * Interface FilamentNotificationEvent.
 */
interface FilamentNotificationEvent
{
    /**
     * Determine if the notifications should be sent.
     *
     * @return bool
     */
    public function shouldSendFilamentNotification(): bool;

    /**
     * Get the filament notification.
     *
     * @return Notification
     */
    public function getFilamentNotification(): Notification;

    /**
     * Get the users to notify.
     *
     * @return Collection
     */
    public function getFilamentNotificationRecipients(): Collection;
}
