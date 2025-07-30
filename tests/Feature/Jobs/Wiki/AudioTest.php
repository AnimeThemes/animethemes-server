<?php

declare(strict_types=1);

use App\Constants\FeatureConstants;
use App\Events\Wiki\Audio\AudioCreated;
use App\Events\Wiki\Audio\AudioDeleted;
use App\Events\Wiki\Audio\AudioRestored;
use App\Events\Wiki\Audio\AudioUpdated;
use App\Jobs\SendDiscordNotificationJob;
use App\Models\Wiki\Audio;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\Event;
use Laravel\Pennant\Feature;

test('audio created sends discord notification', function () {
    Feature::activate(FeatureConstants::ALLOW_DISCORD_NOTIFICATIONS);
    Bus::fake(SendDiscordNotificationJob::class);
    Event::fakeExcept(AudioCreated::class);

    Audio::factory()->createOne();

    Bus::assertDispatched(SendDiscordNotificationJob::class);
});

test('audio deleted sends discord notification', function () {
    $audio = Audio::factory()->createOne();

    Feature::activate(FeatureConstants::ALLOW_DISCORD_NOTIFICATIONS);
    Bus::fake(SendDiscordNotificationJob::class);
    Event::fakeExcept(AudioDeleted::class);

    $audio->delete();

    Bus::assertDispatched(SendDiscordNotificationJob::class);
});

test('audio restored sends discord notification', function () {
    $audio = Audio::factory()->createOne();

    Feature::activate(FeatureConstants::ALLOW_DISCORD_NOTIFICATIONS);
    Bus::fake(SendDiscordNotificationJob::class);
    Event::fakeExcept(AudioRestored::class);

    $audio->restore();

    Bus::assertDispatched(SendDiscordNotificationJob::class);
});

test('audio updated sends discord notification', function () {
    $audio = Audio::factory()->createOne();

    Feature::activate(FeatureConstants::ALLOW_DISCORD_NOTIFICATIONS);
    Bus::fake(SendDiscordNotificationJob::class);
    Event::fakeExcept(AudioUpdated::class);

    $changes = Audio::factory()->makeOne();

    $audio->fill($changes->getAttributes());
    $audio->save();

    Bus::assertDispatched(SendDiscordNotificationJob::class);
});
