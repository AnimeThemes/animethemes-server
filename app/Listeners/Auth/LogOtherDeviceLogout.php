<?php

declare(strict_types=1);

namespace App\Listeners\Auth;

use Illuminate\Auth\Events\OtherDeviceLogout;
use Illuminate\Support\Facades\Log;

/**
 * Class LogOtherDeviceLogout.
 */
class LogOtherDeviceLogout
{
    /**
     * Handle the event.
     *
     * @param  OtherDeviceLogout  $event
     * @return void
     */
    public function handle(OtherDeviceLogout $event): void
    {
        Log::info('Other Device Logout', [
            'guard' => $event->guard,
            'user' => $event->user->getAuthIdentifier(),
        ]);
    }
}
