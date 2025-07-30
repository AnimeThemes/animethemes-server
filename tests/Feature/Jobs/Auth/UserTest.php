<?php

declare(strict_types=1);

use App\Constants\FeatureConstants;
use App\Events\Auth\User\UserCreated;
use App\Events\Auth\User\UserDeleted;
use App\Events\Auth\User\UserRestored;
use App\Events\Auth\User\UserUpdated;
use App\Jobs\SendDiscordNotificationJob;
use App\Models\Auth\User;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\Event;
use Laravel\Pennant\Feature;

test('user created sends discord notification', function () {
    Feature::activate(FeatureConstants::ALLOW_DISCORD_NOTIFICATIONS);
    Bus::fake(SendDiscordNotificationJob::class);
    Event::fakeExcept(UserCreated::class);

    User::factory()->createOne();

    Bus::assertDispatched(SendDiscordNotificationJob::class);
});

test('user deleted sends discord notification', function () {
    $user = User::factory()->createOne();

    Feature::activate(FeatureConstants::ALLOW_DISCORD_NOTIFICATIONS);
    Bus::fake(SendDiscordNotificationJob::class);
    Event::fakeExcept(UserDeleted::class);

    $user->delete();

    Bus::assertDispatched(SendDiscordNotificationJob::class);
});

test('user restored sends discord notification', function () {
    $user = User::factory()->createOne();

    Feature::activate(FeatureConstants::ALLOW_DISCORD_NOTIFICATIONS);
    Bus::fake(SendDiscordNotificationJob::class);
    Event::fakeExcept(UserRestored::class);

    $user->restore();

    Bus::assertDispatched(SendDiscordNotificationJob::class);
});

test('user updated sends discord notification', function () {
    $user = User::factory()->createOne();

    Feature::activate(FeatureConstants::ALLOW_DISCORD_NOTIFICATIONS);
    Bus::fake(SendDiscordNotificationJob::class);
    Event::fakeExcept(UserUpdated::class);

    $changes = User::factory()->makeOne();

    $user->fill($changes->getAttributes());
    $user->save();

    Bus::assertDispatched(SendDiscordNotificationJob::class);
});
