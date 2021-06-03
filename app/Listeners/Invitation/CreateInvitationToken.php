<?php

declare(strict_types=1);

namespace App\Listeners\Invitation;

use App\Events\Invitation\InvitationCreating;
use App\Models\Invitation;
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
