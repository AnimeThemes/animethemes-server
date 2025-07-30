<?php

declare(strict_types=1);

use App\Constants\FeatureConstants;
use App\Events\Wiki\Studio\StudioCreated;
use App\Events\Wiki\Studio\StudioDeleted;
use App\Events\Wiki\Studio\StudioRestored;
use App\Events\Wiki\Studio\StudioUpdated;
use App\Jobs\SendDiscordNotificationJob;
use App\Models\Wiki\Studio;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\Event;
use Laravel\Pennant\Feature;

test('studio created sends discord notification', function () {
    Feature::activate(FeatureConstants::ALLOW_DISCORD_NOTIFICATIONS);
    Bus::fake(SendDiscordNotificationJob::class);
    Event::fakeExcept(StudioCreated::class);

    Studio::factory()->createOne();

    Bus::assertDispatched(SendDiscordNotificationJob::class);
});

test('studio deleted sends discord notification', function () {
    $studio = Studio::factory()->createOne();

    Feature::activate(FeatureConstants::ALLOW_DISCORD_NOTIFICATIONS);
    Bus::fake(SendDiscordNotificationJob::class);
    Event::fakeExcept(StudioDeleted::class);

    $studio->delete();

    Bus::assertDispatched(SendDiscordNotificationJob::class);
});

test('studio restored sends discord notification', function () {
    $studio = Studio::factory()->createOne();

    Feature::activate(FeatureConstants::ALLOW_DISCORD_NOTIFICATIONS);
    Bus::fake(SendDiscordNotificationJob::class);
    Event::fakeExcept(StudioRestored::class);

    $studio->restore();

    Bus::assertDispatched(SendDiscordNotificationJob::class);
});

test('studio updated sends discord notification', function () {
    $studio = Studio::factory()->createOne();

    Feature::activate(FeatureConstants::ALLOW_DISCORD_NOTIFICATIONS);
    Bus::fake(SendDiscordNotificationJob::class);
    Event::fakeExcept(StudioUpdated::class);

    $changes = Studio::factory()->makeOne();

    $studio->fill($changes->getAttributes());
    $studio->save();

    Bus::assertDispatched(SendDiscordNotificationJob::class);
});
