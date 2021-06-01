<?php

declare(strict_types=1);

namespace App\Listeners\Invitation;

use App\Events\Invitation\InvitationEvent;
use App\Mail\InvitationEmail;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Mail;

/**
 * Class SendInvitationMail
 * @package App\Listeners\Invitation
 */
class SendInvitationMail implements ShouldQueue
{
    use InteractsWithQueue;

    /**
     * Handle the event.
     *
     * @param InvitationEvent $event
     * @return void
     */
    public function handle(InvitationEvent $event)
    {
        $invitation = $event->getInvitation();

        Mail::to($invitation->email)->queue(new InvitationEmail($invitation));
    }
}
