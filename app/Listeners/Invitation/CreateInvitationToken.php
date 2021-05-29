<?php

namespace App\Listeners\Invitation;

use App\Events\Invitation\InvitationEvent;
use App\Models\Invitation;

class CreateInvitationToken
{
    /**
     * Handle the event.
     *
     * @param \App\Events\Invitation\InvitationEvent $event
     * @return void
     */
    public function handle(InvitationEvent $event)
    {
        $invitation = $event->getInvitation();

        $invitation->token = Invitation::createToken();
    }
}
