<?php

declare(strict_types=1);

namespace Tests\Feature\Actions\Jetstream;

use App\Models\Auth\User;
use Laravel\Jetstream\Http\Livewire\TeamMemberManager;
use Livewire\Livewire;
use Tests\TestCase;

/**
 * Class RemoveTeamMemberTest.
 */
class RemoveTeamMemberTest extends TestCase
{
    /**
     * Team members can be removed from teams.
     *
     * @return void
     */
    public function testTeamMembersCanBeRemovedFromTeams(): void
    {
        $this->actingAs($user = User::factory()->withPersonalTeam()->createOne());

        $user->currentTeam->users()->attach(
            $otherUser = User::factory()->createOne(),
            ['role' => 'admin']
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
    public function testOnlyTeamOwnerCanRemoveTeamMembers(): void
    {
        $user = User::factory()->withPersonalTeam()->createOne();

        $user->currentTeam->users()->attach(
            $otherUser = User::factory()->createOne(),
            ['role' => 'admin']
        );

        $this->actingAs($otherUser);

        Livewire::test(TeamMemberManager::class, ['team' => $user->currentTeam])
            ->set('teamMemberIdBeingRemoved', $user->id)
            ->call('removeTeamMember')
            ->assertStatus(403);
    }
}
