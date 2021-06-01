<?php declare(strict_types=1);

namespace Jetstream;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Jetstream\Features;
use Laravel\Jetstream\Http\Livewire\DeleteUserForm;
use Livewire\Livewire;
use Tests\TestCase;

/**
 * Class DeleteAccountTest
 * @package Jetstream
 */
class DeleteAccountTest extends TestCase
{
    use RefreshDatabase;

    public function testUserAccountsCanBeDeleted()
    {
        if (! Features::hasAccountDeletionFeatures()) {
            static::markTestSkipped('Account deletion is not enabled.');
        }

        $this->actingAs($user = User::factory()->create());

        Livewire::test(DeleteUserForm::class)
            ->set('password', 'password')
            ->call('deleteUser');

        static::assertNull($user->fresh());
    }

    public function testCorrectPasswordMustBeProvidedBeforeAccountCanBeDeleted()
    {
        if (! Features::hasAccountDeletionFeatures()) {
            static::markTestSkipped('Account deletion is not enabled.');
        }

        $this->actingAs($user = User::factory()->create());

        Livewire::test(DeleteUserForm::class)
                        ->set('password', 'wrong-password')
                        ->call('deleteUser')
                        ->assertHasErrors(['password']);

        static::assertNotNull($user->fresh());
    }
}
