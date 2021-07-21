<?php

declare(strict_types=1);

namespace Tests\Feature\Actions\Jetstream;

use App\Models\Auth\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Jetstream\Http\Livewire\CreateTeamForm;
use Livewire\Livewire;
use Tests\TestCase;

/**
 * Class CreateTeamTest.
 */
class CreateTeamTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Teams cannot be created.
     *
     * @return void
     */
    public function testTeamsCannotBeCreated()
    {
        $this->actingAs($user = User::factory()->withPersonalTeam()->createOne());

        Livewire::test(CreateTeamForm::class)
                    ->set(['state' => ['name' => 'Test Team']])
                    ->call('createTeam');

        static::assertCount(1, $user->fresh()->ownedTeams);
    }
}
