<?php

declare(strict_types=1);

namespace App\Contracts\Events;

use App\Models\Auth\User;
use Illuminate\Support\Collection;
use Laravel\Nova\Notifications\NovaNotification;

/**
 * Interface NovaNotificationEvent.
 */
interface NovaNotificationEvent
{
    /**
     * Determine if the notifications should be sent.
     *
     * @return bool
     */
    public function shouldSend(): bool;

    /**
     * Get the nova notification.
     *
     * @return NovaNotification
     */
    public function getNotification(): NovaNotification;

    /**
     * Get the users to notify.
     *
     * @return Collection<int, User>
     */
    public function getUsers(): Collection;
}
