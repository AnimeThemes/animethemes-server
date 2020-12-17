<?php

namespace Tests\Unit\Models;

use App\Enums\InvitationStatus;
use App\Mail\InvitationEmail;
use App\Models\Invitation;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class InvitationTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    /**
     * The Invitation is open if the Invitation Status is Open.
     *
     * @return void
     */
    public function testInvitationIsOpen()
    {
        $invitation = Invitation::factory()->create([
            'status' => InvitationStatus::OPEN,
        ]);

        $this->assertTrue($invitation->isOpen());
    }

    /**
     * The Invitation is not open if the Invitation Status is not Open.
     *
     * @return void
     */
    public function testInvitationIsClosed()
    {
        $invitation = Invitation::factory()->create([
            'status' => InvitationStatus::CLOSED,
        ]);

        $this->assertFalse($invitation->isOpen());
    }

    /**
     * The Invitation shall have a generated token on creation.
     *
     * @return void
     */
    public function testInvitationCreatesToken()
    {
        $invitation = Invitation::factory()->create();

        $this->assertArrayHasKey('token', $invitation);
    }

    /**
     * The Invitation shall send a InvitationMail after creation.
     *
     * @return void
     */
    public function testInvitationMailWasSent()
    {
        Mail::fake();

        Invitation::factory()->create();

        Mail::assertQueued(InvitationEmail::class);
    }
}
