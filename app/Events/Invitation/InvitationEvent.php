<?php

namespace App\Events\Invitation;

use App\Models\Invitation;

abstract class InvitationEvent
{
    /**
     * The invitation that has fired this event.
     *
     * @var \App\Models\Invitation
     */
    protected $invitation;

    /**
     * Create a new event instance.
     *
     * @param \App\Models\Invitation $invitation
     * @return void
     */
    public function __construct(Invitation $invitation)
    {
        $this->invitation = $invitation;
    }

    /**
     * Get the invitation that has fired this event.
     *
     * @return \App\Models\Invitation
     */
    public function getInvitation()
    {
        return $this->invitation;
    }
}
