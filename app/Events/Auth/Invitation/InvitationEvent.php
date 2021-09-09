<?php

declare(strict_types=1);

namespace App\Events\Auth\Invitation;

use App\Models\Auth\Invitation;

/**
 * Class InvitationEvent.
 */
abstract class InvitationEvent
{
    /**
     * The invitation that has fired this event.
     *
     * @var Invitation
     */
    protected Invitation $invitation;

    /**
     * Create a new event instance.
     *
     * @param  Invitation  $invitation
     * @return void
     */
    public function __construct(Invitation $invitation)
    {
        $this->invitation = $invitation;
    }

    /**
     * Get the invitation that has fired this event.
     *
     * @return Invitation
     */
    public function getInvitation(): Invitation
    {
        return $this->invitation;
    }
}
