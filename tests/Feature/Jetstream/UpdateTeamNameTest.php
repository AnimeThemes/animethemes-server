<?php

declare(strict_types=1);

namespace Jetstream;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Jetstream\Http\Livewire\UpdateTeamNameForm;
use Livewire\Livewire;
use Tests\TestCase;

/**
 * Class UpdateTeamNameTest
 * @package Jetstream
 */
class UpdateTeamNameTest extends TestCase
{
    use RefreshDatabase;

    public function testTeamNamesCanBeUpdated()
    {
        $this->actingAs($user = User::factory()->withPersonalTeam()->create());

        Livewire::test(UpdateTeamNameForm::class, ['team' => $user->currentTeam])
                    ->set(['state' => ['name' => 'Test Team']])
                    ->call('updateTeamName');

        static::assertCount(1, $user->fresh()->ownedTeams);
        static::assertEquals('Test Team', $user->currentTeam->fresh()->name);
    }
}
