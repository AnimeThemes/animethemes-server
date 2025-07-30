<?php

declare(strict_types=1);

use App\Constants\FeatureConstants;
use App\Events\Wiki\Group\GroupCreated;
use App\Events\Wiki\Group\GroupDeleted;
use App\Events\Wiki\Group\GroupRestored;
use App\Events\Wiki\Group\GroupUpdated;
use App\Jobs\SendDiscordNotificationJob;
use App\Models\Wiki\Group;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\Event;
use Laravel\Pennant\Feature;

test('group created sends discord notification', function () {
    Feature::activate(FeatureConstants::ALLOW_DISCORD_NOTIFICATIONS);
    Bus::fake(SendDiscordNotificationJob::class);
    Event::fakeExcept(GroupCreated::class);

    Group::factory()->createOne();

    Bus::assertDispatched(SendDiscordNotificationJob::class);
});

test('group deleted sends discord notification', function () {
    $group = Group::factory()->createOne();

    Feature::activate(FeatureConstants::ALLOW_DISCORD_NOTIFICATIONS);
    Bus::fake(SendDiscordNotificationJob::class);
    Event::fakeExcept(GroupDeleted::class);

    $group->delete();

    Bus::assertDispatched(SendDiscordNotificationJob::class);
});

test('group restored sends discord notification', function () {
    $group = Group::factory()->createOne();

    Feature::activate(FeatureConstants::ALLOW_DISCORD_NOTIFICATIONS);
    Bus::fake(SendDiscordNotificationJob::class);
    Event::fakeExcept(GroupRestored::class);

    $group->restore();

    Bus::assertDispatched(SendDiscordNotificationJob::class);
});

test('group updated sends discord notification', function () {
    $group = Group::factory()->createOne();

    Feature::activate(FeatureConstants::ALLOW_DISCORD_NOTIFICATIONS);
    Bus::fake(SendDiscordNotificationJob::class);
    Event::fakeExcept(GroupUpdated::class);

    $changes = Group::factory()->makeOne();

    $group->fill($changes->getAttributes());
    $group->save();

    Bus::assertDispatched(SendDiscordNotificationJob::class);
});
