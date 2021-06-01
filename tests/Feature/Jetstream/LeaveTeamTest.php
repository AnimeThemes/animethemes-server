<?php

declare(strict_types=1);

namespace Jetstream;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Jetstream\Http\Livewire\TeamMemberManager;
use Livewire\Livewire;
use Tests\TestCase;

/**
 * Class LeaveTeamTest
 * @package Jetstream
 */
class LeaveTeamTest extends TestCase
{
    use RefreshDatabase;

    public function testUsersCanLeaveTeams()
    {
        $user = User::factory()->withPersonalTeam()->create();

        $user->currentTeam->users()->attach(
            $otherUser = User::factory()->create(), ['role' => 'admin']
        );

        $this->actingAs($otherUser);

        Livewire::test(TeamMemberManager::class, ['team' => $user->currentTeam])
            ->call('leaveTeam');

        static::assertCount(0, $user->currentTeam->fresh()->users);
    }

    public function testTeamOwnersCantLeaveTheirOwnTeam()
    {
        $this->actingAs($user = User::factory()->withPersonalTeam()->create());

        Livewire::test(TeamMemberManager::class, ['team' => $user->currentTeam])
            ->call('leaveTeam')
            ->assertHasErrors(['team']);

        static::assertNotNull($user->currentTeam->fresh());
    }
}
