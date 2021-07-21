<?php

declare(strict_types=1);

namespace Tests\Feature\Actions\Jetstream;

use App\Models\Auth\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Hash;
use Laravel\Jetstream\Http\Livewire\UpdatePasswordForm;
use Livewire\Livewire;
use Tests\TestCase;

/**
 * Class UpdatePasswordTest.
 */
class UpdatePasswordTest extends TestCase
{
    use RefreshDatabase;
    use WithFaker;

    /**
     * Password can be updated.
     *
     * @return void
     */
    public function testPasswordCanBeUpdated()
    {
        $this->actingAs($user = User::factory()->createOne());

        $newPassword = $this->faker->password(64, 128);

        Livewire::test(UpdatePasswordForm::class)
                ->set('state', [
                    'current_password' => 'password',
                    'password' => $newPassword,
                    'password_confirmation' => $newPassword,
                ])
                ->call('updatePassword');

        static::assertTrue(Hash::check($newPassword, $user->fresh()->password));
    }

    /**
     * Password must be correct.
     *
     * @return void
     */
    public function testCurrentPasswordMustBeCorrect()
    {
        $this->actingAs($user = User::factory()->createOne());

        $newPassword = $this->faker->password(64, 128);

        Livewire::test(UpdatePasswordForm::class)
                ->set('state', [
                    'current_password' => 'wrong-password',
                    'password' => $newPassword,
                    'password_confirmation' => $newPassword,
                ])
                ->call('updatePassword')
                ->assertHasErrors(['current_password']);

        static::assertTrue(Hash::check('password', $user->fresh()->password));
    }

    /**
     * New passwords must match.
     *
     * @return void
     */
    public function testNewPasswordsMustMatch()
    {
        $this->actingAs($user = User::factory()->createOne());

        Livewire::test(UpdatePasswordForm::class)
                ->set('state', [
                    'current_password' => 'password',
                    'password' => 'new-password',
                    'password_confirmation' => 'wrong-password',
                ])
                ->call('updatePassword')
                ->assertHasErrors(['password']);

        static::assertTrue(Hash::check('password', $user->fresh()->password));
    }
}
