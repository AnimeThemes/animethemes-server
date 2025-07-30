<?php

declare(strict_types=1);

use App\Events\Admin\Announcement\AnnouncementCreated;
use App\Events\Admin\Announcement\AnnouncementDeleted;
use App\Events\Admin\Announcement\AnnouncementUpdated;
use App\Models\Admin\Announcement;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Event;

test('announcement created event dispatched', function () {
    Announcement::factory()->create();

    Event::assertDispatched(AnnouncementCreated::class);
});

test('announcement deleted event dispatched', function () {
    $announcement = Announcement::factory()->create();

    $announcement->delete();

    Event::assertDispatched(AnnouncementDeleted::class);
});

test('announcement updated event dispatched', function () {
    $announcement = Announcement::factory()->createOne();
    $changes = Announcement::factory()->makeOne();

    $announcement->fill($changes->getAttributes());
    $announcement->save();

    Event::assertDispatched(AnnouncementUpdated::class);
});

test('announcement updated event embed fields', function () {
    $announcement = Announcement::factory()->createOne();
    $changes = Announcement::factory()->makeOne();

    $announcement->fill($changes->getAttributes());
    $announcement->save();

    Event::assertDispatched(AnnouncementUpdated::class, function (AnnouncementUpdated $event) {
        $message = $event->getDiscordMessage();

        return ! empty(Arr::get($message->embed, 'fields'));
    });
});
