<?php

namespace Tests\Unit\Mail;

use App\Mail\InvitationEmail;
use App\Models\Invitation;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class InvitationEmailTest extends TestCase
{
    use RefreshDatabase;

    /**
     * An Invitation Email shall contain a registration link.
     *
     * @return void
     */
    public function testContainsRegistrationLink()
    {
        $invitation = Invitation::factory()->create();

        $registrationLink = route('register', ['token' => $invitation->token]);

        $mail = new InvitationEmail($invitation);

        $mail->assertSeeInHtml($registrationLink);
    }
}
