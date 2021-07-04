<?php

declare(strict_types=1);

namespace Tests\Unit\Mail;

use App\Mail\InvitationMail;
use App\Models\Auth\Invitation;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Class InvitationMailTest.
 */
class InvitationMailTest extends TestCase
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

        $mail = new InvitationMail($invitation);

        $mail->assertSeeInHtml($registrationLink);
    }
}
