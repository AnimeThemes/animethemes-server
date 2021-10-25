<?php

declare(strict_types=1);

namespace Tests\Feature\Actions\Fortify;

use App\Models\Auth\User;
use App\Providers\RouteServiceProvider;
use Illuminate\Auth\Events\Verified;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\URL;
use Laravel\Fortify\Features;
use Tests\TestCase;

/**
 * Class EmailVerificationTest.
 */
class EmailVerificationTest extends TestCase
{

    /**
     * Email verification screen can be rendered.
     *
     * @return void
     */
    public function testEmailVerificationScreenCanBeRendered()
    {
        if (! Features::enabled(Features::emailVerification())) {
            static::markTestSkipped('Email verification not enabled.');
        }

        $user = User::factory()->withPersonalTeam()->createOne([
            'email_verified_at' => null,
        ]);

        $response = $this->actingAs($user)->get('/email/verify');

        $response->assertStatus(200);
    }

    /**
     * Email can be verified.
     *
     * @return void
     */
    public function testEmailCanBeVerified()
    {
        if (! Features::enabled(Features::emailVerification())) {
            static::markTestSkipped('Email verification not enabled.');
        }

        Event::fake();

        $user = User::factory()->createOne([
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

    /**
     * Email cannot be verified with invalid hash.
     *
     * @return void
     */
    public function testEmailCanNotBeVerifiedWithInvalidHash()
    {
        if (! Features::enabled(Features::emailVerification())) {
            static::markTestSkipped('Email verification not enabled.');
        }

        $user = User::factory()->createOne([
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
