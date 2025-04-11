<?php

declare(strict_types=1);

namespace App\Contracts\Events;

use Filament\Notifications\Notification;
use Illuminate\Support\Collection;

/**
 * Interface NotifiesUsersEvent.
 */
interface NotifiesUsersEvent
{
    /**
     * Notify the users.
     *
     * @return void
     */
    public function notify(): void;
}
