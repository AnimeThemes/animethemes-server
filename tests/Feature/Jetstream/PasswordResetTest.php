<?php declare(strict_types=1);

namespace Jetstream;

use App\Models\User;
use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

/**
 * Class PasswordResetTest
 * @package Jetstream
 */
class PasswordResetTest extends TestCase
{
    use RefreshDatabase;
    use WithFaker;

    public function testResetPasswordLinkScreenCanBeRendered()
    {
        $response = $this->get('/forgot-password');

        $response->assertStatus(200);
    }

    public function testResetPasswordLinkCanBeRequested()
    {
        Notification::fake();

        $user = User::factory()->create();

        $this->post('/forgot-password', [
            'email' => $user->email,
        ]);

        Notification::assertSentTo($user, ResetPassword::class);
    }

    public function testResetPasswordScreenCanBeRendered()
    {
        Notification::fake();

        $user = User::factory()->create();

        $this->post('/forgot-password', [
            'email' => $user->email,
        ]);

        Notification::assertSentTo($user, ResetPassword::class, function (ResetPassword $notification) {
            $response = $this->get('/reset-password/'.$notification->token);

            $response->assertStatus(200);

            return true;
        });
    }

    public function testPasswordCanBeResetWithValidToken()
    {
        Notification::fake();

        $user = User::factory()->create();

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
