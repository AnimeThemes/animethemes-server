<?php

declare(strict_types=1);

namespace Models;

use App\Models\User;
use Illuminate\Auth\Notifications\VerifyEmail;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\WithoutEvents;
use Illuminate\Support\Facades\Notification;
use Laravel\Sanctum\PersonalAccessToken;
use Tests\TestCase;

/**
 * Class UserTest.
 */
class UserTest extends TestCase
{
    use RefreshDatabase;
    use WithFaker;
    use WithoutEvents;

    /**
     * Users shall have a one-to-many polymorphic relationship to PersonalAccessToken.
     *
     * @return void
     */
    public function testTokens()
    {
        $user = User::factory()->create();

        $user->createToken($this->faker->word());

        static::assertInstanceOf(MorphMany::class, $user->tokens());
        static::assertEquals(1, $user->tokens()->count());
        static::assertInstanceOf(PersonalAccessToken::class, $user->tokens()->first());
    }

    /**
     * Users shall verify email.
     *
     * @return void
     */
    public function testVerificationEmailNotification()
    {
        Notification::fake();

        $user = User::factory()->create();

        $user->sendEmailVerificationNotification();

        Notification::assertSentTo($user, VerifyEmail::class);
    }

    /**
     * Users shall be nameable.
     *
     * @return void
     */
    public function testNameable()
    {
        $user = User::factory()->create();

        static::assertIsString($user->getName());
    }
}
