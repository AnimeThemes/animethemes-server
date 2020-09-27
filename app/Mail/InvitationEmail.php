<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class InvitationEmail extends Mailable
{
    use Queueable, SerializesModels;

    /**
     *
     * @var \App\Models\Invitation
     */
    protected $invitation;

    /**
     * Create a new message instance.
     *
     * @param \App\Models\Invitation  $invitation
     * @return void
     */
    public function __construct($invitation)
    {
        $this->invitation = $invitation;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->subject(__('nova.invitation_subject'))
            ->markdown('email.invitation')
            ->with('url', route('register', ['token' => $this->invitation->token]));
    }
}
