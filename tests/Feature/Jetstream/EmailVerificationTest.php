<?php

declare(strict_types=1);

namespace Jetstream;

use App\Models\User;
use App\Providers\RouteServiceProvider;
use Illuminate\Auth\Events\Verified;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\URL;
use Laravel\Fortify\Features;
use Tests\TestCase;

/**
 * Class EmailVerificationTest
 * @package Jetstream
 */
class EmailVerificationTest extends TestCase
{
    use RefreshDatabase;

    public function testEmailVerificationScreenCanBeRendered()
    {
        if (! Features::enabled(Features::emailVerification())) {
            static::markTestSkipped('Email verification not enabled.');
        }

        $user = User::factory()->withPersonalTeam()->create([
            'email_verified_at' => null,
        ]);

        $response = $this->actingAs($user)->get('/email/verify');

        $response->assertStatus(200);
    }

    public function testEmailCanBeVerified()
    {
        if (! Features::enabled(Features::emailVerification())) {
            static::markTestSkipped('Email verification not enabled.');
        }

        Event::fake();

        $user = User::factory()->create([
            'email_verified_at' => null,
        ]);

        $verificationUrl = URL::temporarySignedRoute(
            'verification.verify',
            now()->addMinutes(60),
            ['id' => $user->id, 'hash' => sha1($user->email)]
        );

        $response = $this->actingAs($user)->get($verificationUrl);

        Event::assertDispatched(Verified::class);

        static::assertTrue($user->fresh()->hasVerifiedEmail());
        $response->assertRedirect(RouteServiceProvider::HOME.'?verified=1');
    }

    public function testEmailCanNotBeVerifiedWithInvalidHash()
    {
        if (! Features::enabled(Features::emailVerification())) {
            static::markTestSkipped('Email verification not enabled.');
        }

        $user = User::factory()->create([
            'email_verified_at' => null,
        ]);

        $verificationUrl = URL::temporarySignedRoute(
            'verification.verify',
            now()->addMinutes(60),
            ['id' => $user->id, 'hash' => sha1('wrong-email')]
        );

        $this->actingAs($user)->get($verificationUrl);

        static::assertFalse($user->fresh()->hasVerifiedEmail());
    }
}
