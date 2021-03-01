<?php

namespace Tests\Unit\Nova\Actions;

use App\Enums\InvitationStatus;
use App\Mail\InvitationEmail;
use App\Models\Invitation;
use App\Nova\Actions\ResendInvitationAction;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Mail;
use JoshGaber\NovaUnit\Actions\NovaActionTest;
use Laravel\Nova\Fields\ActionFields;
use Tests\TestCase;

class ResendInvitationTest extends TestCase
{
    use NovaActionTest, RefreshDatabase, WithFaker;

    /**
     * The Resend Invitation Action shall have no fields.
     *
     * @return void
     */
    public function testHasNoFields()
    {
        $this->novaAction(ResendInvitationAction::class)
            ->assertHasNoFields();
    }

    /**
     * The Resend Invitation Action shall return a dangerous message if no invitations were resent.
     *
     * @return void
     */
    public function testNoInvitationsResent()
    {
        $action = $this->novaAction(ResendInvitationAction::class);

        $action->handle([], collect())
            ->assertDanger(__('nova.resent_invitations_for_none'));
    }

    /**
     * The Resend Invitation Action shall return a dangerous message if no invitations were resent.
     *
     * @return void
     */
    public function testNoClosedInvitationsResent()
    {
        $invitations = Invitation::factory()
            ->count($this->faker->randomDigitNotNull)
            ->create([
                'status' => InvitationStatus::CLOSED,
            ]);

        $action = $this->novaAction(ResendInvitationAction::class);

        $action->handle([], $invitations)
            ->assertDanger(__('nova.resent_invitations_for_none'));
    }

    /**
     * The Resend Invitation Action shall return a dangerous message if no invitations were resent.
     *
     * @return void
     */
    public function testOpenInvitationsResent()
    {
        $invitations = Invitation::factory()
            ->count($this->faker->randomDigitNotNull)
            ->create([
                'status' => InvitationStatus::OPEN,
            ]);

        $action = $this->novaAction(ResendInvitationAction::class);

        $action->handle([], $invitations)
            ->assertMessage(__('nova.resent_invitations_for_none', ['users' => $invitations->pluck('name')->join(',')]));
    }

    /**
     * The Resend Invitation Action shall not send emails for closed invitations.
     *
     * @return void
     */
    public function testNoMailSentForClosedInvitations()
    {
        $invitations = Invitation::factory()
            ->count($this->faker->randomDigitNotNull)
            ->create([
                'status' => InvitationStatus::CLOSED,
            ]);

        Mail::fake();

        $action = ResendInvitationAction::make();

        $action->handle(new ActionFields(collect(), collect()), $invitations);

        Mail::assertNotQueued(InvitationEmail::class);
    }

    /**
     * The Resend Invitation Action shall send emails for open invitations.
     *
     * @return void
     */
    public function testMailSentForOpenInvitations()
    {
        $invitation_count = $this->faker->randomDigitNotNull;

        $invitations = Invitation::factory()
            ->count($invitation_count)
            ->create([
                'status' => InvitationStatus::OPEN,
            ]);

        Mail::fake();

        $action = ResendInvitationAction::make();

        $action->handle(new ActionFields(collect(), collect()), $invitations);

        Mail::assertQueued(InvitationEmail::class, $invitation_count);
    }

    /**
     * The Resend Invitation Action shall change invitation tokens.
     *
     * @return void
     */
    public function testInvitationTokenChanged()
    {
        $invitations = Invitation::factory()
            ->count($this->faker->randomDigitNotNull)
            ->create([
                'status' => InvitationStatus::OPEN,
            ]);

        $old_tokens = $invitations->pluck('token');

        $action = ResendInvitationAction::make();

        $action->handle(new ActionFields(collect(), collect()), $invitations);

        foreach ($invitations as $invitation) {
            $this->assertNotContains($invitation->token, $old_tokens);
        }
    }
}
