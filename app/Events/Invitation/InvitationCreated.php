<?php

namespace App\Events\Invitation;

use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class InvitationCreated extends InvitationEvent
{
    use Dispatchable, SerializesModels;
}
