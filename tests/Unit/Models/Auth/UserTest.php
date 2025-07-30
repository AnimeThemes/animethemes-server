<?php

declare(strict_types=1);

use App\Models\Auth\User;
use App\Models\List\Playlist;
use Illuminate\Auth\Notifications\VerifyEmail;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Support\Facades\Notification;
use Laravel\Sanctum\PersonalAccessToken;

uses(Illuminate\Foundation\Testing\WithFaker::class);

test('tokens', function () {
    $user = User::factory()->createOne();

    $user->createToken(fake()->word());

    static::assertInstanceOf(MorphMany::class, $user->tokens());
    static::assertEquals(1, $user->tokens()->count());
    static::assertInstanceOf(PersonalAccessToken::class, $user->tokens()->first());
});

test('verification email notification', function () {
    $user = User::factory()->createOne();

    $user->sendEmailVerificationNotification();

    Notification::assertSentTo($user, VerifyEmail::class);
});

test('nameable', function () {
    $user = User::factory()->createOne();

    static::assertIsString($user->getName());
});

test('has subtitle', function () {
    $user = User::factory()->createOne();

    static::assertIsString($user->getSubtitle());
});

test('playlists', function () {
    $playlistCount = fake()->randomDigitNotNull();

    $user = User::factory()
        ->has(Playlist::factory()->count($playlistCount))
        ->createOne();

    static::assertInstanceOf(HasMany::class, $user->playlists());
    static::assertEquals($playlistCount, $user->playlists()->count());
    static::assertInstanceOf(Playlist::class, $user->playlists()->first());
});
