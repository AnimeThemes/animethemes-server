<?php

declare(strict_types=1);

namespace Tests\Feature\Actions\Jetstream;

use App\Models\Auth\Team;
use App\Models\Auth\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Jetstream\Http\Livewire\DeleteTeamForm;
use Livewire\Livewire;
use Tests\TestCase;

/**
 * Class DeleteTeamTest.
 */
class DeleteTeamTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Teams can be deleted.
     *
     * @return void
     */
    public function testTeamsCanBeDeleted()
    {
        $this->actingAs($user = User::factory()->withPersonalTeam()->createOne());

        $user->ownedTeams()->save($team = Team::factory()->makeOne([
            'personal_team' => false,
        ]));

        $team->users()->attach(
            $otherUser = User::factory()->createOne(),
            ['role' => 'test-role']
        );

        Livewire::test(DeleteTeamForm::class, ['team' => $team->fresh()])
            ->call('deleteTeam');

        static::assertNull($team->fresh());
        static::assertCount(0, $otherUser->fresh()->teams);
    }

    /**
     * Personal teams cannot be deleted.
     *
     * @return void
     */
    public function testPersonalTeamsCanNotBeDeleted()
    {
        $this->actingAs($user = User::factory()->withPersonalTeam()->createOne());

        Livewire::test(DeleteTeamForm::class, ['team' => $user->currentTeam])
            ->call('deleteTeam')
            ->assertHasErrors(['team']);

        static::assertNotNull($user->currentTeam->fresh());
    }
}
