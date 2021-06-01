<?php declare(strict_types=1);

namespace Events;

use App\Events\Announcement\AnnouncementCreated;
use App\Events\Announcement\AnnouncementDeleted;
use App\Events\Announcement\AnnouncementRestored;
use App\Events\Announcement\AnnouncementUpdated;
use App\Models\Announcement;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Event;
use Tests\TestCase;

/**
 * Class AnnouncementTest
 * @package Events
 */
class AnnouncementTest extends TestCase
{
    use RefreshDatabase;

    /**
     * When an Announcement is created, an AnnouncementCreated event shall be dispatched.
     *
     * @return void
     */
    public function testAnnouncementCreatedEventDispatched()
    {
        Event::fake();

        Announcement::factory()->create();

        Event::assertDispatched(AnnouncementCreated::class);
    }

    /**
     * When an Announcement is deleted, an AnnouncementDeleted event shall be dispatched.
     *
     * @return void
     */
    public function testAnnouncementDeletedEventDispatched()
    {
        Event::fake();

        $announcement = Announcement::factory()->create();

        $announcement->delete();

        Event::assertDispatched(AnnouncementDeleted::class);
    }

    /**
     * When an Announcement is restored, an AnnouncementRestored event shall be dispatched.
     *
     * @return void
     */
    public function testAnnouncementRestoredEventDispatched()
    {
        Event::fake();

        $announcement = Announcement::factory()->create();

        $announcement->restore();

        Event::assertDispatched(AnnouncementRestored::class);
    }

    /**
     * When an Announcement is updated, an AnnouncementUpdated event shall be dispatched.
     *
     * @return void
     */
    public function testAnnouncementUpdatedEventDispatched()
    {
        Event::fake();

        $announcement = Announcement::factory()->create();
        $changes = Announcement::factory()->make();

        $announcement->fill($changes->getAttributes());
        $announcement->save();

        Event::assertDispatched(AnnouncementUpdated::class);
    }
}
