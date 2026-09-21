<?php

declare(strict_types=1);

use App\Models\Auth\User;
use App\Models\List\ExternalProfile;
use App\Models\List\Playlist;
use App\Models\User\Notification as UserNotification;
use Illuminate\Auth\Notifications\VerifyEmail;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Notification;
use Laravel\Sanctum\PersonalAccessToken;

pest()->use(WithFaker::class);

test('tokens', function (): void {
    $user = User::factory()->createOne();

    $user->createToken(fake()->word());

    expect($user->tokens())->toBeInstanceOf(MorphMany::class);
    expect($user->tokens()->count())->toEqual(1);
    expect($user->tokens()->first())->toBeInstanceOf(PersonalAccessToken::class);
});

test('verification email notification', function (): void {
    $user = User::factory()->createOne();

    $user->sendEmailVerificationNotification();

    Notification::assertSentTo($user, VerifyEmail::class);
});

test('nameable', function (): void {
    $user = User::factory()->createOne();

    expect($user->getName())->toBeString();
});

test('has subtitle', function (): void {
    $user = User::factory()->createOne();

    expect($user->getSubtitle())->toBeString();
});

test('playlists', function (): void {
    $playlistCount = fake()->randomDigitNotNull();

    $user = User::factory()
        ->has(Playlist::factory()->count($playlistCount))
        ->createOne();

    expect($user->playlists())->toBeInstanceOf(HasMany::class);
    expect($user->playlists()->count())->toEqual($playlistCount);
    expect($user->playlists()->first())->toBeInstanceOf(Playlist::class);
});

test('external profiles', function (): void {
    $profileCount = fake()->randomDigitNotNull();

    $user = User::factory()
        ->has(ExternalProfile::factory()->count($profileCount))
        ->createOne();

    expect($user->externalprofiles())->toBeInstanceOf(HasMany::class);
    expect($user->externalprofiles()->count())->toEqual($profileCount);
    expect($user->externalprofiles()->first())->toBeInstanceOf(ExternalProfile::class);
});

test('notifications', function (): void {
    $notificationCount = fake()->randomDigitNotNull();

    $user = User::factory()
        ->has(UserNotification::factory()->count($notificationCount))
        ->createOne();

    expect($user->notifications())->toBeInstanceOf(MorphMany::class);
    expect($user->notifications()->count())->toEqual($notificationCount);
    expect($user->notifications()->first())->toBeInstanceOf(UserNotification::class);
});
