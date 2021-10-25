<?php

declare(strict_types=1);

namespace Tests\Feature\Actions\Jetstream;

use App\Models\Auth\User;
use Illuminate\Support\Facades\Mail;
use Laravel\Jetstream\Http\Livewire\TeamMemberManager;
use Laravel\Jetstream\Mail\TeamInvitation;
use Livewire\Livewire;
use Tests\TestCase;

/**
 * Class InviteTeamMemberTest.
 */
class InviteTeamMemberTest extends TestCase
{

    /**
     * Team members can be invited to team.
     *
     * @return void
     */
    public function testTeamMembersCanBeInvitedToTeam()
    {
        Mail::fake();

        $this->actingAs($user = User::factory()->withPersonalTeam()->createOne());

        Livewire::test(TeamMemberManager::class, ['team' => $user->currentTeam])
            ->set('addTeamMemberForm', [
                'email' => 'test@example.com',
                'role' => 'admin',
            ])->call('addTeamMember');

        Mail::assertSent(TeamInvitation::class);

        static::assertCount(1, $user->currentTeam->fresh()->teamInvitations);
    }

    /**
     * Team member invitations can be cancelled.
     *
     * @return void
     */
    public function testTeamMemberInvitationsCanBeCancelled()
    {
        $this->actingAs($user = User::factory()->withPersonalTeam()->createOne());

        // Add the team member...
        $component = Livewire::test(TeamMemberManager::class, ['team' => $user->currentTeam])
                        ->set('addTeamMemberForm', [
                            'email' => 'test@example.com',
                            'role' => 'admin',
                        ])->call('addTeamMember');

        $invitationId = $user->currentTeam->fresh()->teamInvitations->first()->id;

        // Cancel the team invitation...
        $component->call('cancelTeamInvitation', $invitationId);

        static::assertCount(0, $user->currentTeam->fresh()->teamInvitations);
    }
}
