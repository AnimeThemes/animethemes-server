<?php

declare(strict_types=1);

namespace Tests\Feature\Actions\Jetstream;

use App\Models\Auth\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Jetstream\Features;
use Laravel\Jetstream\Http\Livewire\DeleteUserForm;
use Livewire\Livewire;
use Tests\TestCase;

/**
 * Class DeleteAccountTest.
 */
class DeleteAccountTest extends TestCase
{
    use RefreshDatabase;

    /**
     * User accounts can be deleted.
     *
     * @return void
     */
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

    /**
     * Correct password must be provided before account can be deleted.
     *
     * @return void
     */
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
