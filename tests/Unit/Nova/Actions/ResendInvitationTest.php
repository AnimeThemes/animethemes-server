<?php declare(strict_types=1);

namespace Nova\Actions;

use App\Enums\InvitationStatus;
use App\Mail\InvitationEmail;
use App\Models\Invitation;
use App\Nova\Actions\ResendInvitationAction;
use Exception;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Mail;
use JoshGaber\NovaUnit\Actions\InvalidNovaActionException;
use JoshGaber\NovaUnit\Actions\NovaActionTest;
use Laravel\Nova\Fields\ActionFields;
use Tests\TestCase;

/**
 * Class ResendInvitationTest
 * @package Nova\Actions
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
            ->count($this->faker->randomDigitNotNull)
            ->create([
                'status' => InvitationStatus::CLOSED,
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
            ->count($this->faker->randomDigitNotNull)
            ->create([
                'status' => InvitationStatus::OPEN,
            ]);

        $action = static::novaAction(ResendInvitationAction::class);

        $action->handle([], $invitations)
            ->assertMessage(__('nova.resent_invitations_for_none', ['users' => $invitations->pluck('name')->join(',')]));
    }

    /**
     * The Resend Invitation Action shall not send emails for closed invitations.
     *
     * @return void
     * @throws Exception
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
     * @throws Exception
     */
    public function testMailSentForOpenInvitations()
    {
        $invitationCount = $this->faker->randomDigitNotNull;

        $invitations = Invitation::factory()
            ->count($invitationCount)
            ->create([
                'status' => InvitationStatus::OPEN,
            ]);

        Mail::fake();

        $action = ResendInvitationAction::make();

        $action->handle(new ActionFields(collect(), collect()), $invitations);

        Mail::assertQueued(InvitationEmail::class, $invitationCount);
    }

    /**
     * The Resend Invitation Action shall change invitation tokens.
     *
     * @return void
     * @throws Exception
     */
    public function testInvitationTokenChanged()
    {
        $invitations = Invitation::factory()
            ->count($this->faker->randomDigitNotNull)
            ->create([
                'status' => InvitationStatus::OPEN,
            ]);

        $oldTokens = $invitations->pluck('token');

        $action = ResendInvitationAction::make();

        $action->handle(new ActionFields(collect(), collect()), $invitations);

        foreach ($invitations as $invitation) {
            static::assertNotContains($invitation->token, $oldTokens);
        }
    }
}
