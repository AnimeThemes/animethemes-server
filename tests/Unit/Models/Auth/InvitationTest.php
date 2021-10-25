<?php

declare(strict_types=1);

namespace Tests\Unit\Models\Auth;

use App\Enums\Models\Auth\InvitationStatus;
use App\Mail\InvitationMail;
use App\Models\Auth\Invitation;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

/**
 * Class InvitationTest.
 */
class InvitationTest extends TestCase
{

    /**
     * The status attribute of an invitation shall be cast to an InvitationStatus enum instance.
     *
     * @return void
     */
    public function testCastsStatusToEnum()
    {
        $invitation = Invitation::factory()->createOne();

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

        $invitation = Invitation::factory()->createOne();

        static::assertEquals(1, $invitation->audits()->count());
    }

    /**
     * Invitations shall be nameable.
     *
     * @return void
     */
    public function testNameable()
    {
        $invitation = Invitation::factory()->createOne();

        static::assertIsString($invitation->getName());
    }

    /**
     * The Invitation is open if the Invitation Status is Open.
     *
     * @return void
     */
    public function testInvitationIsOpen()
    {
        $invitation = Invitation::factory()->createOne([
            Invitation::ATTRIBUTE_STATUS => InvitationStatus::OPEN,
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
        $invitation = Invitation::factory()->createOne([
            Invitation::ATTRIBUTE_STATUS => InvitationStatus::CLOSED,
        ]);

        static::assertFalse($invitation->isOpen());
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

        Mail::assertQueued(InvitationMail::class);
    }
}
