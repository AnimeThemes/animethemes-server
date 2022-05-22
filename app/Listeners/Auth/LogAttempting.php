<?php

declare(strict_types=1);

namespace App\Listeners\Auth;

use Illuminate\Auth\Events\Attempting;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Log;

/**
 * Class LogAttempting.
 */
class LogAttempting
{
    /**
     * Handle the event.
     *
     * @param  Attempting  $event
     * @return void
     */
    public function handle(Attempting $event): void
    {
        Log::info('Attempting authentication', [
            'guard' => $event->guard,
            'remember' => $event->remember,
            'credentials' => Arr::except($event->credentials, 'password'),
        ]);
    }
}
