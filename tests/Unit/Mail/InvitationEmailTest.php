<?php

namespace Tests\Unit\Mail;

use App\Mail\InvitationEmail;
use App\Models\Invitation;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class InvitationEmailTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    /**
     * An Invitation Email shall contain a registration link.
     *
     * @return void
     */
    public function testContainsRegistrationLink()
    {
        $invitation = Invitation::factory()->create();

        $registration_link = route('register', ['token' => $invitation->token]);

        $mail = new InvitationEmail($invitation);

        $mail->assertSeeInHtml($registration_link);
    }
}
