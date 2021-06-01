<?php declare(strict_types=1);

namespace Jetstream;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Jetstream\Http\Livewire\UpdateProfileInformationForm;
use Livewire\Livewire;
use Tests\TestCase;

/**
 * Class ProfileInformationTest
 * @package Jetstream
 */
class ProfileInformationTest extends TestCase
{
    use RefreshDatabase;

    public function testCurrentProfileInformationIsAvailable()
    {
        $this->actingAs($user = User::factory()->create());

        $component = Livewire::test(UpdateProfileInformationForm::class);

        static::assertEquals($user->name, $component->state['name']);
        static::assertEquals($user->email, $component->state['email']);
    }

    public function testProfileInformationCanBeUpdated()
    {
        $this->actingAs($user = User::factory()->create());

        Livewire::test(UpdateProfileInformationForm::class)
                ->set('state', ['name' => 'Test Name', 'email' => 'test@example.com'])
                ->call('updateProfileInformation');

        static::assertEquals('Test Name', $user->fresh()->name);
        static::assertEquals('test@example.com', $user->fresh()->email);
    }
}
