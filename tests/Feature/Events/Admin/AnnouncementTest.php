<?php

declare(strict_types=1);

namespace Tests\Feature\Events\Admin;

use App\Events\Admin\Announcement\AnnouncementCreated;
use App\Events\Admin\Announcement\AnnouncementDeleted;
use App\Events\Admin\Announcement\AnnouncementUpdated;
use App\Models\Admin\Announcement;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Event;
use Tests\TestCase;

class AnnouncementTest extends TestCase
{
    /**
     * When an Announcement is created, an AnnouncementCreated event shall be dispatched.
     */
    public function testAnnouncementCreatedEventDispatched(): void
    {
        Announcement::factory()->create();

        Event::assertDispatched(AnnouncementCreated::class);
    }

    /**
     * When an Announcement is deleted, an AnnouncementDeleted event shall be dispatched.
     */
    public function testAnnouncementDeletedEventDispatched(): void
    {
        $announcement = Announcement::factory()->create();

        $announcement->delete();

        Event::assertDispatched(AnnouncementDeleted::class);
    }

    /**
     * When an Announcement is updated, an AnnouncementUpdated event shall be dispatched.
     */
    public function testAnnouncementUpdatedEventDispatched(): void
    {
        $announcement = Announcement::factory()->createOne();
        $changes = Announcement::factory()->makeOne();

        $announcement->fill($changes->getAttributes());
        $announcement->save();

        Event::assertDispatched(AnnouncementUpdated::class);
    }

    /**
     * The AnnouncementUpdated event shall contain embed fields.
     */
    public function testAnnouncementUpdatedEventEmbedFields(): void
    {
        $announcement = Announcement::factory()->createOne();
        $changes = Announcement::factory()->makeOne();

        $announcement->fill($changes->getAttributes());
        $announcement->save();

        Event::assertDispatched(AnnouncementUpdated::class, function (AnnouncementUpdated $event) {
            $message = $event->getDiscordMessage();

            return ! empty(Arr::get($message->embed, 'fields'));
        });
    }
}
