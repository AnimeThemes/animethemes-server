<?php

declare(strict_types=1);

namespace App\Listeners\Auth;

use Illuminate\Auth\Events\Registered;
use Illuminate\Support\Facades\Log;

/**
 * Class LogRegistered.
 */
class LogRegistered
{
    /**
     * Handle the event.
     *
     * @param  Registered  $event
     * @return void
     */
    public function handle(Registered $event): void
    {
        Log::info('Registered', [
            'user' => $event->user->getAuthIdentifier(),
        ]);
    }
}
