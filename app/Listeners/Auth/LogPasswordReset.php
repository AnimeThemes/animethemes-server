<?php

declare(strict_types=1);

namespace App\Listeners\Auth;

use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Support\Facades\Log;

class LogPasswordReset
{
    public function handle(PasswordReset $event): void
    {
        Log::info('Password Reset', [
            'user' => $event->user->getAuthIdentifier(),
        ]);
    }
}
