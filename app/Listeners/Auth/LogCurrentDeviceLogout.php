<?php

declare(strict_types=1);

namespace App\Listeners\Auth;

use Illuminate\Auth\Events\CurrentDeviceLogout;
use Illuminate\Support\Facades\Log;

class LogCurrentDeviceLogout
{
    public function handle(CurrentDeviceLogout $event): void
    {
        Log::info('Current Device Logout', [
            'guard' => $event->guard,
            'user' => $event->user->getAuthIdentifier(),
        ]);
    }
}
