<?php

declare(strict_types=1);

namespace Tests\Feature\Actions\Jetstream;

use App\Models\Auth\User;
use Laravel\Jetstream\Http\Livewire\TeamMemberManager;
use Livewire\Livewire;
use Tests\TestCase;

/**
 * Class UpdateTeamMemberRoleTest.
 */
class UpdateTeamMemberRoleTest extends TestCase
{

    /**
     * Team member roles can be updated.
     *
     * @return void
     */
    public function testTeamMemberRolesCanBeUpdated()
    {
        $this->actingAs($user = User::factory()->withPersonalTeam()->createOne());

        $user->currentTeam->users()->attach(
            $otherUser = User::factory()->createOne(),
            ['role' => 'admin']
        );

        Livewire::test(TeamMemberManager::class, ['team' => $user->currentTeam])
            ->set('managingRoleFor', $otherUser)
            ->set('currentRole', 'editor')
            ->call('updateRole');

        static::assertTrue($otherUser->fresh()->hasTeamRole($user->currentTeam->fresh(), 'editor'));
    }

    /**
     * Only team owner can update team member roles.
     *
     * @return void
     */
    public function testOnlyTeamOwnerCanUpdateTeamMemberRoles()
    {
        $user = User::factory()->withPersonalTeam()->createOne();

        $user->currentTeam->users()->attach($otherUser = User::factory()->createOne(), ['role' => 'admin']);

        $this->actingAs($otherUser);

        Livewire::test(TeamMemberManager::class, ['team' => $user->currentTeam])
            ->set('managingRoleFor', $otherUser)
            ->set('currentRole', 'editor')
            ->call('updateRole')
            ->assertStatus(403);

        static::assertTrue($otherUser->fresh()->hasTeamRole($user->currentTeam->fresh(), 'admin'));
    }
}
