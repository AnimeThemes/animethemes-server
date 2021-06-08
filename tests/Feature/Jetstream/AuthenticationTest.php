<?php

declare(strict_types=1);

namespace Jetstream;

use App\Models\User;
use App\Providers\RouteServiceProvider;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Class AuthenticationTest.
 */
class AuthenticationTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Login screen can be rendered.
     *
     * @return void
     */
    public function testLoginScreenCanBeRendered()
    {
        $response = $this->get('/login');

        $response->assertStatus(200);
    }

    /**
     * Users can authenticate using the login screen.
     *
     * @return void
     */
    public function testUsersCanAuthenticateUsingTheLoginScreen()
    {
        $user = User::factory()->create();

        $response = $this->post('/login', [
            'email' => $user->email,
            'password' => 'password',
        ]);

        $this->assertAuthenticated();
        $response->assertRedirect(RouteServiceProvider::HOME);
    }

    /**
     * Users cannot authenticate with invalid password.
     *
     * @return void
     */
    public function testUsersCanNotAuthenticateWithInvalidPassword()
    {
        $user = User::factory()->create();

        $this->post('/login', [
            'email' => $user->email,
            'password' => 'wrong-password',
        ]);

        $this->assertGuest();
    }
}
