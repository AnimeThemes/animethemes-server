<?php

declare(strict_types=1);

namespace App\Listeners\Auth;

use Illuminate\Auth\Events\Lockout;
use Illuminate\Support\Facades\Log;

class LogLockout
{
    /**
     * @noinspection PhpUnusedParameterInspection
     */
    public function handle(Lockout $event): void
    {
        Log::info('Authentication lockout');
    }
}
