<?php

declare(strict_types=1);

namespace App\Contracts\Events;

interface NotifiesUsersEvent
{
    public function notify(): void;
}
