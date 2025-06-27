<?php

declare(strict_types=1);

namespace App\Contracts\Events;

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
