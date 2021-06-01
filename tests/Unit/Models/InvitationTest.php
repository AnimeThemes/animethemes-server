<?php

declare(strict_types=1);

namespace Models;

use App\Enums\InvitationStatus;
use App\Mail\InvitationEmail;
use App\Models\Invitation;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

/**
 * Class InvitationTest.
 */
class InvitationTest extends TestCase
{
    use RefreshDatabase;

    /**
     * The status attribute of an invitation shall be cast to an InvitationStatus enum instance.
     *
     * @return void
     */
    public function testCastsStatusToEnum()
    {
        $invitation = Invitation::factory()->create();

        $status = $invitation->status;

        static::assertInstanceOf(InvitationStatus::class, $status);
    }

    /**
     * Invitations shall be auditable.
     *
     * @return void
     */
    public function testAuditable()
    {
        Config::set('audit.console', true);

        $invitation = Invitation::factory()->create();

        static::assertEquals(1, $invitation->audits->count());
    }

    /**
     * Invitations shall be nameable.
     *
     * @return void
     */
    public function testNameable()
    {
        $invitation = Invitation::factory()->create();

        static::assertIsString($invitation->getName());
    }

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

        static::assertTrue($invitation->isOpen());
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

        static::assertFalse($invitation->isOpen());
    }

    /**
     * The Invitation shall have a generated token on creation.
     *
     * @return void
     */
    public function testInvitationCreatesToken()
    {
        $invitation = Invitation::factory()->create();

        static::assertArrayHasKey('token', $invitation);
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
