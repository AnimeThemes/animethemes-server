<?php

declare(strict_types=1);

use App\Constants\FeatureConstants;
use App\Events\Wiki\ExternalResource\ExternalResourceCreated;
use App\Events\Wiki\ExternalResource\ExternalResourceDeleted;
use App\Events\Wiki\ExternalResource\ExternalResourceRestored;
use App\Events\Wiki\ExternalResource\ExternalResourceUpdated;
use App\Jobs\SendDiscordNotificationJob;
use App\Models\Wiki\ExternalResource;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\Event;
use Laravel\Pennant\Feature;

test('resource created sends discord notification', function () {
    Feature::activate(FeatureConstants::ALLOW_DISCORD_NOTIFICATIONS);
    Bus::fake(SendDiscordNotificationJob::class);
    Event::fakeExcept(ExternalResourceCreated::class);

    ExternalResource::factory()->createOne();

    Bus::assertDispatched(SendDiscordNotificationJob::class);
});

test('resource deleted sends discord notification', function () {
    $resource = ExternalResource::factory()->createOne();

    Feature::activate(FeatureConstants::ALLOW_DISCORD_NOTIFICATIONS);
    Bus::fake(SendDiscordNotificationJob::class);
    Event::fakeExcept(ExternalResourceDeleted::class);

    $resource->delete();

    Bus::assertDispatched(SendDiscordNotificationJob::class);
});

test('resource restored sends discord notification', function () {
    $resource = ExternalResource::factory()->createOne();

    Feature::activate(FeatureConstants::ALLOW_DISCORD_NOTIFICATIONS);
    Bus::fake(SendDiscordNotificationJob::class);
    Event::fakeExcept(ExternalResourceRestored::class);

    $resource->restore();

    Bus::assertDispatched(SendDiscordNotificationJob::class);
});

test('resource updated sends discord notification', function () {
    $resource = ExternalResource::factory()->createOne();

    Feature::activate(FeatureConstants::ALLOW_DISCORD_NOTIFICATIONS);
    Bus::fake(SendDiscordNotificationJob::class);
    Event::fakeExcept(ExternalResourceUpdated::class);

    $changes = ExternalResource::factory()->makeOne();

    $resource->fill($changes->getAttributes());
    $resource->save();

    Bus::assertDispatched(SendDiscordNotificationJob::class);
});
