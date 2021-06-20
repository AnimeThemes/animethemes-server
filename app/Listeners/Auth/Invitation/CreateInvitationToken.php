<?php

declare(strict_types=1);

namespace App\Listeners\Auth\Invitation;

use App\Events\Auth\Invitation\InvitationCreating;
use App\Models\Auth\Invitation;
use Exception;

/**
 * Class CreateInvitationToken.
 */
class CreateInvitationToken
{
    /**
     * Handle the event.
     *
     * @param InvitationCreating $event
     * @return void
     * @throws Exception
     */
    public function handle(InvitationCreating $event)
    {
        $invitation = $event->getInvitation();

        $invitation->token = Invitation::createToken();
    }
}
