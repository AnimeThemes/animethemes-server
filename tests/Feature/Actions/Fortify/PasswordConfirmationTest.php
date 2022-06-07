<?php

declare(strict_types=1);

namespace Tests\Feature\Actions\Fortify;

use App\Models\Auth\User;
use Tests\TestCase;

/**
 * Class PasswordConfirmationTest.
 */
class PasswordConfirmationTest extends TestCase
{
    /**
     * Confirm password screen can be rendered.
     *
     * @return void
     */
    public function testConfirmPasswordScreenCanBeRendered(): void
    {
        $user = User::factory()->createOne();

        $response = $this->actingAs($user)->get(route('password.confirm'));

        $response->assertStatus(200);
    }

    /**
     * Password can be confirmed.
     *
     * @return void
     */
    public function testPasswordCanBeConfirmed(): void
    {
        $user = User::factory()->createOne();

        $response = $this->actingAs($user)->post(route('password.confirm'), [
            'password' => 'password',
        ]);

        $response->assertRedirect();
        $response->assertSessionHasNoErrors();
    }

    /**
     * Password is not confirmed with invalid password.
     *
     * @return void
     */
    public function testPasswordIsNotConfirmedWithInvalidPassword(): void
    {
        $user = User::factory()->createOne();

        $response = $this->actingAs($user)->post(route('password.confirm'), [
            'password' => 'wrong-password',
        ]);

        $response->assertSessionHasErrors();
    }
}
