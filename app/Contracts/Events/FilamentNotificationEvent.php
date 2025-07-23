<?php

declare(strict_types=1);

namespace App\Contracts\Events;

use Filament\Notifications\Notification;
use Illuminate\Support\Collection;

interface FilamentNotificationEvent
{
    /**
     * Determine if the notifications should be sent.
     */
    public function shouldSendFilamentNotification(): bool;

    /**
     * Get the filament notification.
     */
    public function getFilamentNotification(): Notification;

    /**
     * Get the users to notify.
     *
     * @return Collection
     */
    public function getFilamentNotificationRecipients(): Collection;
}
