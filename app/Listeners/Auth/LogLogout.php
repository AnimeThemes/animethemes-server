<?php

declare(strict_types=1);

namespace App\Listeners\Auth;

use Illuminate\Auth\Events\Logout;
use Illuminate\Support\Facades\Log;

class LogLogout
{
    /**
     * Handle the event.
     */
    public function handle(Logout $event): void
    {
        Log::info('Logout', [
            'guard' => $event->guard,
            'user' => $event->user->getAuthIdentifier(),
        ]);
    }
}
