<?php

declare(strict_types=1);

namespace Jetstream;

use App\Models\Auth\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Jetstream\Features;
use Tests\TestCase;

/**
 * Class PasswordConfirmationTest.
 */
class PasswordConfirmationTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Confirm password screen can be rendered.
     *
     * @return void
     */
    public function testConfirmPasswordScreenCanBeRendered()
    {
        $user = Features::hasTeamFeatures()
                        ? User::factory()->withPersonalTeam()->create()
                        : User::factory()->create();

        $response = $this->actingAs($user)->get('/user/confirm-password');

        $response->assertStatus(200);
    }

    /**
     * Password can be confirmed.
     *
     * @return void
     */
    public function testPasswordCanBeConfirmed()
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->post('/user/confirm-password', [
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
    public function testPasswordIsNotConfirmedWithInvalidPassword()
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->post('/user/confirm-password', [
            'password' => 'wrong-password',
        ]);

        $response->assertSessionHasErrors();
    }
}
