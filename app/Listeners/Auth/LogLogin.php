<?php

declare(strict_types=1);

namespace App\Listeners\Auth;

use Illuminate\Auth\Events\Login;
use Illuminate\Support\Facades\Log;

/**
 * Class LogLogin.
 */
class LogLogin
{
    /**
     * Handle the event.
     *
     * @param  Login  $event
     * @return void
     */
    public function handle(Login $event): void
    {
        Log::info('Login', [
            'guard' => $event->guard,
            'remember' => $event->remember,
            'user' => $event->user->getAuthIdentifier(),
        ]);
    }
}
