<?php

declare(strict_types=1);

namespace App\Contracts\Events;

interface NotifiesUsersEvent
{
    /**
     * Notify the users.
     */
    public function notify(): void;
}
