<?php

declare(strict_types=1);

namespace App\Listeners\Auth;

use Illuminate\Auth\Events\Logout;
use Illuminate\Support\Facades\Log;

/**
 * Class LogLogout.
 */
class LogLogout
{
    /**
     * Handle the event.
     *
     * @param  Logout  $event
     * @return void
     */
    public function handle(Logout $event): void
    {
        Log::info('Logout', [
            'guard' => $event->guard,
            'user' => $event->user->getAuthIdentifier(),
        ]);
    }
}
