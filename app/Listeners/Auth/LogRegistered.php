<?php

declare(strict_types=1);

namespace App\Listeners\Auth;

use Illuminate\Auth\Events\Registered;
use Illuminate\Support\Facades\Log;

class LogRegistered
{
    /**
     * Handle the event.
     */
    public function handle(Registered $event): void
    {
        Log::info('Registered', [
            'user' => $event->user->getAuthIdentifier(),
        ]);
    }
}
