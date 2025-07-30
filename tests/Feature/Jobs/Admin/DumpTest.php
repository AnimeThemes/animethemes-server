<?php

declare(strict_types=1);

use App\Constants\FeatureConstants;
use App\Events\Admin\Dump\DumpCreated;
use App\Events\Admin\Dump\DumpDeleted;
use App\Events\Admin\Dump\DumpUpdated;
use App\Jobs\SendDiscordNotificationJob;
use App\Models\Admin\Dump;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\Event;
use Laravel\Pennant\Feature;

test('dump created sends discord notification', function () {
    Feature::activate(FeatureConstants::ALLOW_DISCORD_NOTIFICATIONS);
    Bus::fake(SendDiscordNotificationJob::class);
    Event::fakeExcept(DumpCreated::class);

    Dump::factory()->createOne();

    Bus::assertDispatched(SendDiscordNotificationJob::class);
});

test('dump deleted sends discord notification', function () {
    $dump = Dump::factory()->createOne();

    Feature::activate(FeatureConstants::ALLOW_DISCORD_NOTIFICATIONS);
    Bus::fake(SendDiscordNotificationJob::class);
    Event::fakeExcept(DumpDeleted::class);

    $dump->delete();

    Bus::assertDispatched(SendDiscordNotificationJob::class);
});

test('dump updated sends discord notification', function () {
    $dump = Dump::factory()->createOne();

    Feature::activate(FeatureConstants::ALLOW_DISCORD_NOTIFICATIONS);
    Bus::fake(SendDiscordNotificationJob::class);
    Event::fakeExcept(DumpUpdated::class);

    $changes = Dump::factory()->makeOne();

    $dump->fill($changes->getAttributes());
    $dump->save();

    Bus::assertDispatched(SendDiscordNotificationJob::class);
});
