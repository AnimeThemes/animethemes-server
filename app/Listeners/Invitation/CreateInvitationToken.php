<?php

declare(strict_types=1);

namespace App\Listeners\Invitation;

use App\Events\Invitation\InvitationEvent;
use App\Models\Invitation;
use Exception;

/**
 * Class CreateInvitationToken
 * @package App\Listeners\Invitation
 */
class CreateInvitationToken
{
    /**
     * Handle the event.
     *
     * @param InvitationEvent $event
     * @return void
     * @throws Exception
     */
    public function handle(InvitationEvent $event)
    {
        $invitation = $event->getInvitation();

        $invitation->token = Invitation::createToken();
    }
}
