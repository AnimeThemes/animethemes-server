<?php

declare(strict_types=1);

namespace Tests\Feature\Events\Admin;

use App\Events\Admin\Announcement\AnnouncementCreated;
use App\Events\Admin\Announcement\AnnouncementDeleted;
use App\Events\Admin\Announcement\AnnouncementRestored;
use App\Events\Admin\Announcement\AnnouncementUpdated;
use App\Models\Admin\Announcement;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Event;
use Tests\TestCase;

/**
 * Class AnnouncementTest.
 */
class AnnouncementTest extends TestCase
{
    /**
     * When an Announcement is created, an AnnouncementCreated event shall be dispatched.
     *
     * @return void
     */
    public function testAnnouncementCreatedEventDispatched(): void
    {
        Announcement::factory()->create();

        Event::assertDispatched(AnnouncementCreated::class);
    }

    /**
     * When an Announcement is deleted, an AnnouncementDeleted event shall be dispatched.
     *
     * @return void
     */
    public function testAnnouncementDeletedEventDispatched(): void
    {
        $announcement = Announcement::factory()->create();

        $announcement->delete();

        Event::assertDispatched(AnnouncementDeleted::class);
    }

    /**
     * When an Announcement is restored, an AnnouncementRestored event shall be dispatched.
     *
     * @return void
     */
    public function testAnnouncementRestoredEventDispatched(): void
    {
        $announcement = Announcement::factory()->createOne();

        $announcement->restore();

        Event::assertDispatched(AnnouncementRestored::class);
    }

    /**
     * When an Announcement is restored, an AnnouncementUpdated event shall not be dispatched.
     * Note: This is a customization that overrides default framework behavior.
     * An updated event is fired on restore.
     *
     * @return void
     */
    public function testAnnouncementRestoresQuietly(): void
    {
        $announcement = Announcement::factory()->createOne();

        $announcement->restore();

        Event::assertNotDispatched(AnnouncementUpdated::class);
    }

    /**
     * When an Announcement is updated, an AnnouncementUpdated event shall be dispatched.
     *
     * @return void
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
     *
     * @return void
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
