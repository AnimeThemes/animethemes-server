<?php

declare(strict_types=1);

namespace Tests\Feature\Actions\Fortify;

use App\Models\Auth\User;
use App\Providers\RouteServiceProvider;
use Tests\TestCase;

/**
 * Class AuthenticationTest.
 */
class AuthenticationTest extends TestCase
{
    /**
     * Login screen can be rendered.
     *
     * @return void
     */
    public function testLoginScreenCanBeRendered(): void
    {
        $response = $this->get('/login');

        $response->assertStatus(200);
    }

    /**
     * Users can authenticate using the login screen.
     *
     * @return void
     */
    public function testUsersCanAuthenticateUsingTheLoginScreen(): void
    {
        $user = User::factory()->createOne();

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
    public function testUsersCanNotAuthenticateWithInvalidPassword(): void
    {
        $user = User::factory()->createOne();

        $this->post('/login', [
            'email' => $user->email,
            'password' => 'wrong-password',
        ]);

        $this->assertGuest();
    }
}
