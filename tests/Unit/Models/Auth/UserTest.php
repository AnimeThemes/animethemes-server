<?php

declare(strict_types=1);

namespace Tests\Unit\Models\Auth;

use App\Models\Auth\User;
use App\Models\List\Playlist;
use Illuminate\Auth\Notifications\VerifyEmail;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Notification;
use Laravel\Sanctum\PersonalAccessToken;
use Tests\TestCase;

/**
 * Class UserTest.
 */
class UserTest extends TestCase
{
    use WithFaker;

    /**
     * Users shall have a one-to-many polymorphic relationship to PersonalAccessToken.
     *
     * @return void
     */
    public function testTokens(): void
    {
        $user = User::factory()->createOne();

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
    public function testVerificationEmailNotification(): void
    {
        $user = User::factory()->createOne();

        $user->sendEmailVerificationNotification();

        Notification::assertSentTo($user, VerifyEmail::class);
    }

    /**
     * Users shall be nameable.
     *
     * @return void
     */
    public function testNameable(): void
    {
        $user = User::factory()->createOne();

        static::assertIsString($user->getName());
    }

    /**
     * Users shall have subtitle.
     *
     * @return void
     */
    public function testHasSubtitle(): void
    {
        $user = User::factory()->createOne();

        static::assertIsString($user->getSubtitle());
    }

    /**
     * User shall have a one-to-many relationship with the type Playlist.
     *
     * @return void
     */
    public function testPlaylists(): void
    {
        $playlistCount = $this->faker->randomDigitNotNull();

        $user = User::factory()
            ->has(Playlist::factory()->count($playlistCount))
            ->createOne();

        static::assertInstanceOf(HasMany::class, $user->playlists());
        static::assertEquals($playlistCount, $user->playlists()->count());
        static::assertInstanceOf(Playlist::class, $user->playlists()->first());
    }
}
