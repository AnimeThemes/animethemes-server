<?php

declare(strict_types=1);

use App\Constants\FeatureConstants;
use App\Events\Admin\Announcement\AnnouncementCreated;
use App\Events\Admin\Announcement\AnnouncementDeleted;
use App\Events\Admin\Announcement\AnnouncementUpdated;
use App\Jobs\SendDiscordNotificationJob;
use App\Models\Admin\Announcement;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\Event;
use Laravel\Pennant\Feature;

test('announcement created sends discord notification', function () {
    Feature::activate(FeatureConstants::ALLOW_DISCORD_NOTIFICATIONS);
    Bus::fake(SendDiscordNotificationJob::class);
    Event::fakeExcept(AnnouncementCreated::class);

    Announcement::factory()->createOne();

    Bus::assertDispatched(SendDiscordNotificationJob::class);
});

test('announcement deleted sends discord notification', function () {
    $announcement = Announcement::factory()->createOne();

    Feature::activate(FeatureConstants::ALLOW_DISCORD_NOTIFICATIONS);
    Bus::fake(SendDiscordNotificationJob::class);
    Event::fakeExcept(AnnouncementDeleted::class);

    $announcement->delete();

    Bus::assertDispatched(SendDiscordNotificationJob::class);
});

test('announcement updated sends discord notification', function () {
    $announcement = Announcement::factory()->createOne();

    Feature::activate(FeatureConstants::ALLOW_DISCORD_NOTIFICATIONS);
    Bus::fake(SendDiscordNotificationJob::class);
    Event::fakeExcept(AnnouncementUpdated::class);

    $changes = Announcement::factory()->makeOne();

    $announcement->fill($changes->getAttributes());
    $announcement->save();

    Bus::assertDispatched(SendDiscordNotificationJob::class);
});
