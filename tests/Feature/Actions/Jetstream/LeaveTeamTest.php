<?php

declare(strict_types=1);

namespace Tests\Feature\Actions\Jetstream;

use App\Models\Auth\User;
use Laravel\Jetstream\Http\Livewire\TeamMemberManager;
use Livewire\Livewire;
use Tests\TestCase;

/**
 * Class LeaveTeamTest.
 */
class LeaveTeamTest extends TestCase
{
    /**
     * Users can leave teams.
     *
     * @return void
     */
    public function testUsersCanLeaveTeams()
    {
        $user = User::factory()->withPersonalTeam()->createOne();

        $user->currentTeam->users()->attach(
            $otherUser = User::factory()->createOne(),
            ['role' => 'admin']
        );

        $this->actingAs($otherUser);

        Livewire::test(TeamMemberManager::class, ['team' => $user->currentTeam])
            ->call('leaveTeam');

        static::assertCount(0, $user->currentTeam->fresh()->users);
    }

    /**
     * Team owners cannot leave their own team.
     *
     * @return void
     */
    public function testTeamOwnersCanNotLeaveTheirOwnTeam()
    {
        $this->actingAs($user = User::factory()->withPersonalTeam()->createOne());

        Livewire::test(TeamMemberManager::class, ['team' => $user->currentTeam])
            ->call('leaveTeam')
            ->assertHasErrors(['team']);

        static::assertNotNull($user->currentTeam->fresh());
    }
}
