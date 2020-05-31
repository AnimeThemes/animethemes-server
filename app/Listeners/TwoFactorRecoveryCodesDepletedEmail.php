<?php

namespace App\Listeners;

use App\Mail\RecoveryCodesMail;
use DarkGhostHunter\Laraguard\Events\TwoFactorRecoveryCodesDepleted;
use Illuminate\Support\Facades\Mail;

class TwoFactorRecoveryCodesDepletedEmail
{
    /**
     * Handle the event.
     *
     * @param  object  $event
     * @return void
     */
    public function handle(TwoFactorRecoveryCodesDepleted $twoFactorRecoveryCodesDepleted)
    {
        Mail::to($twoFactorRecoveryCodesDepleted->user->email)->queue(new RecoveryCodesMail($twoFactorRecoveryCodesDepleted->user));
    }
}
