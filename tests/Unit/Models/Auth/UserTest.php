<?php

declare(strict_types=1);

use App\Models\Auth\User;
use App\Models\List\ExternalProfile;
use App\Models\List\Playlist;
use App\Models\User\Like;
use App\Models\User\Notification as UserNotification;
use App\Models\User\Submission;
use App\Models\User\WatchHistory;
use Illuminate\Auth\Notifications\VerifyEmail;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Support\Facades\Notification;
use Laravel\Sanctum\PersonalAccessToken;

uses(Illuminate\Foundation\Testing\WithFaker::class);

test('tokens', function () {
    $user = User::factory()->createOne();

    $user->createToken(fake()->word());

    $this->assertInstanceOf(MorphMany::class, $user->tokens());
    $this->assertEquals(1, $user->tokens()->count());
    $this->assertInstanceOf(PersonalAccessToken::class, $user->tokens()->first());
});

test('verification email notification', function () {
    $user = User::factory()->createOne();

    $user->sendEmailVerificationNotification();

    Notification::assertSentTo($user, VerifyEmail::class);
});

test('nameable', function () {
    $user = User::factory()->createOne();

    $this->assertIsString($user->getName());
});

test('has subtitle', function () {
    $user = User::factory()->createOne();

    $this->assertIsString($user->getSubtitle());
});

test('playlists', function () {
    $playlistCount = fake()->randomDigitNotNull();

    $user = User::factory()
        ->has(Playlist::factory()->count($playlistCount))
        ->createOne();

    $this->assertInstanceOf(HasMany::class, $user->playlists());
    $this->assertEquals($playlistCount, $user->playlists()->count());
    $this->assertInstanceOf(Playlist::class, $user->playlists()->first());
});

test('external profiles', function () {
    $profileCount = fake()->randomDigitNotNull();

    $user = User::factory()
        ->has(ExternalProfile::factory()->count($profileCount))
        ->createOne();

    $this->assertInstanceOf(HasMany::class, $user->externalprofiles());
    $this->assertEquals($profileCount, $user->externalprofiles()->count());
    $this->assertInstanceOf(ExternalProfile::class, $user->externalprofiles()->first());
});

test('likes', function () {
    $likeCount = fake()->randomDigitNotNull();

    $user = User::factory()
        ->has(Like::factory()->count($likeCount)->forEntry())
        ->createOne();

    $this->assertInstanceOf(HasMany::class, $user->likes());
    $this->assertEquals($likeCount, $user->likes()->count());
    $this->assertInstanceOf(Like::class, $user->likes()->first());
});

test('submissions', function () {
    $submissionCount = fake()->randomDigitNotNull();

    $user = User::factory()
        ->has(Submission::factory()->count($submissionCount))
        ->createOne();

    $this->assertInstanceOf(HasMany::class, $user->submissions());
    $this->assertEquals($submissionCount, $user->submissions()->count());
    $this->assertInstanceOf(Submission::class, $user->submissions()->first());
});

test('notifications', function () {
    $notificationCount = fake()->randomDigitNotNull();

    $user = User::factory()
        ->has(UserNotification::factory()->count($notificationCount))
        ->createOne();

    $this->assertInstanceOf(MorphMany::class, $user->notifications());
    $this->assertEquals($notificationCount, $user->notifications()->count());
    $this->assertInstanceOf(UserNotification::class, $user->notifications()->first());
});

test('watch history', function () {
    $historyCount = fake()->randomDigitNotNull();

    $user = User::factory()
        ->has(WatchHistory::factory()->count($historyCount))
        ->createOne();

    $this->assertInstanceOf(HasMany::class, $user->watchHistory());
    $this->assertEquals($historyCount, $user->watchHistory()->count());
    $this->assertInstanceOf(WatchHistory::class, $user->watchHistory()->first());
});
