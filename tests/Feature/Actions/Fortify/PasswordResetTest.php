<?php

declare(strict_types=1);

namespace Tests\Feature\Actions\Fortify;

use App\Models\Auth\User;
use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Notification;
use Laravel\Fortify\Features;
use Tests\TestCase;

/**
 * Class PasswordResetTest.
 */
class PasswordResetTest extends TestCase
{
    use RefreshDatabase;
    use WithFaker;

    /**
     * Reset password link screen can be rendered.
     *
     * @return void
     */
    public function testResetPasswordLinkScreenCanBeRendered()
    {
        if (! Features::enabled(Features::updatePasswords())) {
            static::markTestSkipped('Password updates are not enabled.');
        }

        $response = $this->get('/forgot-password');

        $response->assertStatus(200);
    }

    /**
     * Reset password link can be requested.
     *
     * @return void
     */
    public function testResetPasswordLinkCanBeRequested()
    {
        if (! Features::enabled(Features::updatePasswords())) {
            static::markTestSkipped('Password updates are not enabled.');
        }

        Notification::fake();

        $user = User::factory()->createOne();

        $this->post('/forgot-password', [
            'email' => $user->email,
        ]);

        Notification::assertSentTo($user, ResetPassword::class);
    }

    /**
     * Reset password screen can be rendered.
     *
     * @return void
     */
    public function testResetPasswordScreenCanBeRendered()
    {
        if (! Features::enabled(Features::updatePasswords())) {
            static::markTestSkipped('Password updates are not enabled.');
        }

        Notification::fake();

        $user = User::factory()->createOne();

        $this->post('/forgot-password', [
            'email' => $user->email,
        ]);

        Notification::assertSentTo($user, ResetPassword::class, function (ResetPassword $notification) {
            $response = $this->get('/reset-password/'.$notification->token);

            $response->assertStatus(200);

            return true;
        });
    }

    /**
     * Password can be reset with valid token.
     *
     * @return void
     */
    public function testPasswordCanBeResetWithValidToken()
    {
        if (! Features::enabled(Features::updatePasswords())) {
            static::markTestSkipped('Password updates are not enabled.');
        }

        Notification::fake();

        $user = User::factory()->createOne();

        $this->post('/forgot-password', [
            'email' => $user->email,
        ]);

        $strongPassword = $this->faker->password(64, 128);

        Notification::assertSentTo($user, ResetPassword::class, function (ResetPassword $notification) use ($user, $strongPassword) {
            $response = $this->post('/reset-password', [
                'token' => $notification->token,
                'email' => $user->email,
                'password' => $strongPassword,
                'password_confirmation' => $strongPassword,
            ]);

            $response->assertSessionHasNoErrors();

            return true;
        });
    }
}
