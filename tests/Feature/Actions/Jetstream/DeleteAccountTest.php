<?php

declare(strict_types=1);

namespace Tests\Feature\Actions\Jetstream;

use App\Models\Auth\User;
use Laravel\Jetstream\Features;
use Laravel\Jetstream\Http\Livewire\DeleteUserForm;
use Livewire\Livewire;
use Tests\TestCase;

/**
 * Class DeleteAccountTest.
 */
class DeleteAccountTest extends TestCase
{
    /**
     * User accounts can be deleted.
     *
     * @return void
     */
    public function testUserAccountsCanBeDeleted(): void
    {
        if (! Features::hasAccountDeletionFeatures()) {
            static::markTestSkipped('Account deletion is not enabled.');
        }

        $this->actingAs($user = User::factory()->createOne());

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
    public function testCorrectPasswordMustBeProvidedBeforeAccountCanBeDeleted(): void
    {
        if (! Features::hasAccountDeletionFeatures()) {
            static::markTestSkipped('Account deletion is not enabled.');
        }

        $this->actingAs($user = User::factory()->createOne());

        Livewire::test(DeleteUserForm::class)
                        ->set('password', 'wrong-password')
                        ->call('deleteUser')
                        ->assertHasErrors(['password']);

        static::assertNotNull($user->fresh());
    }
}
