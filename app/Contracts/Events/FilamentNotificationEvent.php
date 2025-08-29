<?php

declare(strict_types=1);

namespace App\Contracts\Events;

use Filament\Notifications\Notification;
use Illuminate\Support\Collection;

interface FilamentNotificationEvent
{
    public function shouldSendFilamentNotification(): bool;

    public function getFilamentNotification(): Notification;

    /**
     * @return Collection
     */
    public function getFilamentNotificationRecipients(): Collection;
}
