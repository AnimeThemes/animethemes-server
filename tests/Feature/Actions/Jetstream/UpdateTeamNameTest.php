<?php

declare(strict_types=1);

namespace Tests\Feature\Actions\Jetstream;

use App\Models\Auth\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Jetstream\Http\Livewire\UpdateTeamNameForm;
use Livewire\Livewire;
use Tests\TestCase;

/**
 * Class UpdateTeamNameTest.
 */
class UpdateTeamNameTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Team names can be updated.
     *
     * @return void
     */
    public function testTeamNamesCanBeUpdated()
    {
        $this->actingAs($user = User::factory()->withPersonalTeam()->createOne());

        Livewire::test(UpdateTeamNameForm::class, ['team' => $user->currentTeam])
                    ->set(['state' => ['name' => 'Test Team']])
                    ->call('updateTeamName');

        static::assertCount(1, $user->fresh()->ownedTeams);
        static::assertEquals('Test Team', $user->currentTeam->fresh()->name);
    }
}
