<?php

declare(strict_types=1);

namespace Jetstream;

use App\Models\Team;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Jetstream\Http\Livewire\DeleteTeamForm;
use Livewire\Livewire;
use Tests\TestCase;

/**
 * Class DeleteTeamTest
 * @package Jetstream
 */
class DeleteTeamTest extends TestCase
{
    use RefreshDatabase;

    public function testTeamsCanBeDeleted()
    {
        $this->actingAs($user = User::factory()->withPersonalTeam()->create());

        $user->ownedTeams()->save($team = Team::factory()->make([
            'personal_team' => false,
        ]));

        $team->users()->attach(
            $otherUser = User::factory()->create(), ['role' => 'test-role']
        );

        Livewire::test(DeleteTeamForm::class, ['team' => $team->fresh()])
            ->call('deleteTeam');

        static::assertNull($team->fresh());
        static::assertCount(0, $otherUser->fresh()->teams);
    }

    public function testPersonalTeamsCantBeDeleted()
    {
        $this->actingAs($user = User::factory()->withPersonalTeam()->create());

        Livewire::test(DeleteTeamForm::class, ['team' => $user->currentTeam])
            ->call('deleteTeam')
            ->assertHasErrors(['team']);

        static::assertNotNull($user->currentTeam->fresh());
    }
}
