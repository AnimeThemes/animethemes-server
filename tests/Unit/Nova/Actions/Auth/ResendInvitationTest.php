<?php

declare(strict_types=1);

namespace Tests\Unit\Nova\Actions\Auth;

use App\Enums\Models\Auth\InvitationStatus;
use App\Mail\InvitationMail;
use App\Models\Auth\Invitation;
use App\Nova\Actions\Auth\ResendInvitationAction;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Mail;
use JoshGaber\NovaUnit\Actions\InvalidNovaActionException;
use JoshGaber\NovaUnit\Actions\NovaActionTest;
use Laravel\Nova\Fields\ActionFields;
use Tests\TestCase;

/**
 * Class ResendInvitationTest.
 */
class ResendInvitationTest extends TestCase
{
    use NovaActionTest;
    use RefreshDatabase;
    use WithFaker;

    /**
     * The Resend Invitation Action shall have no fields.
     *
     * @return void
     * @throws InvalidNovaActionException
     */
    public function testHasNoFields()
    {
        static::novaAction(ResendInvitationAction::class)
            ->assertHasNoFields();
    }

    /**
     * The Resend Invitation Action shall return a dangerous message if no invitations were resent.
     *
     * @return void
     * @throws InvalidNovaActionException
     */
    public function testNoInvitationsResent()
    {
        $action = static::novaAction(ResendInvitationAction::class);

        $action->handle([], collect())
            ->assertDanger(__('nova.resent_invitations_for_none'));
    }

    /**
     * The Resend Invitation Action shall return a dangerous message if no invitations were resent.
     *
     * @return void
     * @throws InvalidNovaActionException
     */
    public function testNoClosedInvitationsResent()
    {
        $invitations = Invitation::factory()
            ->count($this->faker->randomDigitNotNull())
            ->create([
                Invitation::ATTRIBUTE_STATUS => InvitationStatus::CLOSED,
            ]);

        $action = static::novaAction(ResendInvitationAction::class);

        $action->handle([], $invitations)
            ->assertDanger(__('nova.resent_invitations_for_none'));
    }

    /**
     * The Resend Invitation Action shall return a dangerous message if no invitations were resent.
     *
     * @return void
     * @throws InvalidNovaActionException
     */
    public function testOpenInvitationsResent()
    {
        $invitations = Invitation::factory()
            ->count($this->faker->randomDigitNotNull())
            ->create([
                Invitation::ATTRIBUTE_STATUS => InvitationStatus::OPEN,
            ]);

        $action = static::novaAction(ResendInvitationAction::class);

        $users = $invitations->pluck(Invitation::ATTRIBUTE_NAME)->join(',');

        $action->handle([], $invitations)
            ->assertMessage(__('nova.resent_invitations_for_users', ['users' => $users]));
    }

    /**
     * The Resend Invitation Action shall not send emails for closed invitations.
     *
     * @return void
     */
    public function testNoMailSentForClosedInvitations()
    {
        $invitations = Invitation::factory()
            ->count($this->faker->randomDigitNotNull())
            ->create([
                Invitation::ATTRIBUTE_STATUS => InvitationStatus::CLOSED,
            ]);

        Mail::fake();

        $action = ResendInvitationAction::make();

        $action->handle(new ActionFields(collect(), collect()), $invitations);

        Mail::assertNotQueued(InvitationMail::class);
    }

    /**
     * The Resend Invitation Action shall send emails for open invitations.
     *
     * @return void
     */
    public function testMailSentForOpenInvitations()
    {
        $invitationCount = $this->faker->randomDigitNotNull();

        $invitations = Invitation::factory()
            ->count($invitationCount)
            ->create([
                Invitation::ATTRIBUTE_STATUS => InvitationStatus::OPEN,
            ]);

        Mail::fake();

        $action = ResendInvitationAction::make();

        $action->handle(new ActionFields(collect(), collect()), $invitations);

        Mail::assertQueued(InvitationMail::class, $invitationCount);
    }
}
