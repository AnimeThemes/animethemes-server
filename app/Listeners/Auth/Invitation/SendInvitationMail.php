<?php

declare(strict_types=1);

namespace App\Listeners\Auth\Invitation;

use App\Events\Auth\Invitation\InvitationCreated;
use App\Mail\InvitationMail;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Mail;

/**
 * Class SendInvitationMail.
 */
class SendInvitationMail implements ShouldQueue
{
    use InteractsWithQueue;

    /**
     * Handle the event.
     *
     * @param  InvitationCreated  $event
     * @return void
     */
    public function handle(InvitationCreated $event): void
    {
        $invitation = $event->getModel();

        Mail::to($invitation->email)->queue(new InvitationMail($invitation));
    }
}
