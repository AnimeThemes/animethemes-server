<?php

declare(strict_types=1);

namespace App\Listeners\Auth;

use Illuminate\Auth\Events\Failed;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Log;

/**
 * Class LogFailed.
 */
class LogFailed
{
    /**
     * Handle the event.
     *
     * @param  Failed  $event
     * @return void
     */
    public function handle(Failed $event): void
    {
        Log::info('Authentication attempt failed', [
            'guard' => $event->guard,
            'user' => $event->user?->getAuthIdentifier(),
            'credentials' => Arr::except($event->credentials, 'password'),
        ]);
    }
}
