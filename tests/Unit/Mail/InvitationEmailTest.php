<?php

declare(strict_types=1);

namespace Mail;

use App\Mail\InvitationEmail;
use App\Models\Invitation;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Class InvitationEmailTest.
 */
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
