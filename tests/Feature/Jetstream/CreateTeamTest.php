<?php

namespace Tests\Feature\Jetstream;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Jetstream\Http\Livewire\CreateTeamForm;
use Livewire\Livewire;
use Tests\TestCase;

class CreateTeamTest extends TestCase
{
    use RefreshDatabase;

    public function test_teams_cannot_be_created()
    {
        $this->actingAs($user = User::factory()->withPersonalTeam()->create());

        Livewire::test(CreateTeamForm::class)
                    ->set(['state' => ['name' => 'Test Team']])
                    ->call('createTeam');

        $this->assertCount(1, $user->fresh()->ownedTeams);
    }
}
