<?php

namespace App\Observers;

use App\Mail\InvitationEmail;
use App\Models\Invitation;
use Illuminate\Support\Facades\Mail;

class InvitationObserver
{
    /**
     * Handle the app models invitation "creating" event.
     *
     * @param  \App\Models\Invitation  $invitation
     * @return void
     */
    public function creating(Invitation $invitation)
    {
        $invitation->token = Invitation::createToken();
    }

    /**
     * Handle the app models invitation "created" event.
     *
     * @param  \App\Models\Invitation  $invitation
     * @return void
     */
    public function created(Invitation $invitation)
    {
        Mail::to($invitation->email)->queue(new InvitationEmail($invitation));
    }
}
