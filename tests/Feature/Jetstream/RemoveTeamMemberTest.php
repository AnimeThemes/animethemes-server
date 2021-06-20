<?php

declare(strict_types=1);

namespace Jetstream;

use App\Models\Auth\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Jetstream\Http\Livewire\TeamMemberManager;
use Livewire\Livewire;
use Tests\TestCase;

/**
 * Class RemoveTeamMemberTest.
 */
class RemoveTeamMemberTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Team members can be removed from teams.
     *
     * @return void
     */
    public function testTeamMembersCanBeRemovedFromTeams()
    {
        $this->actingAs($user = User::factory()->withPersonalTeam()->create());

        $user->currentTeam->users()->attach(
            $otherUser = User::factory()->create(), ['role' => 'admin']
        );

        Livewire::test(TeamMemberManager::class, ['team' => $user->currentTeam])
            ->set('teamMemberIdBeingRemoved', $otherUser->id)
            ->call('removeTeamMember');

        static::assertCount(0, $user->currentTeam->fresh()->users);
    }

    /**
     * Only team owner can remove team members.
     *
     * @return void
     */
    public function testOnlyTeamOwnerCanRemoveTeamMembers()
    {
        $user = User::factory()->withPersonalTeam()->create();

        $user->currentTeam->users()->attach(
            $otherUser = User::factory()->create(), ['role' => 'admin']
        );

        $this->actingAs($otherUser);

        Livewire::test(TeamMemberManager::class, ['team' => $user->currentTeam])
            ->set('teamMemberIdBeingRemoved', $user->id)
            ->call('removeTeamMember')
            ->assertStatus(403);
    }
}
