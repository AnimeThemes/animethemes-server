<?php

namespace App\Listeners;

use App\Mail\RecoveryCodesMail;
use DarkGhostHunter\Laraguard\Events\TwoFactorEnabled;
use Illuminate\Support\Facades\Mail;

class TwoFactorEnabledRecoveryCodesEmail
{
    /**
     * Handle the event.
     *
     * @param  object  $event
     * @return void
     */
    public function handle(TwoFactorEnabled $twoFactorEnabled)
    {
        Mail::to($twoFactorEnabled->user->email)->queue(new RecoveryCodesMail($twoFactorEnabled->user));
    }
}
