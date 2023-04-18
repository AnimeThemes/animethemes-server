<?php

declare(strict_types=1);

namespace Tests\Feature\Jobs\Admin;

use App\Constants\Config\FlagConstants;
use App\Events\Admin\Announcement\AnnouncementCreated;
use App\Events\Admin\Announcement\AnnouncementDeleted;
use App\Events\Admin\Announcement\AnnouncementRestored;
use App\Events\Admin\Announcement\AnnouncementUpdated;
use App\Jobs\SendDiscordNotificationJob;
use App\Models\Admin\Announcement;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Event;
use Tests\TestCase;

/**
 * Class AnnouncementTest.
 */
class AnnouncementTest extends TestCase
{
    /**
     * When an announcement is created, a SendDiscordNotification job shall be dispatched.
     *
     * @return void
     */
    public function testAnnouncementCreatedSendsDiscordNotification(): void
    {
        Config::set(FlagConstants::ALLOW_DISCORD_NOTIFICATIONS_FLAG_QUALIFIED, true);
        Bus::fake(SendDiscordNotificationJob::class);
        Event::fakeExcept(AnnouncementCreated::class);

        Announcement::factory()->createOne();

        Bus::assertDispatched(SendDiscordNotificationJob::class);
    }

    /**
     * When an announcement is deleted, a SendDiscordNotification job shall be dispatched.
     *
     * @return void
     */
    public function testAnnouncementDeletedSendsDiscordNotification(): void
    {
        $announcement = Announcement::factory()->createOne();

        Config::set(FlagConstants::ALLOW_DISCORD_NOTIFICATIONS_FLAG_QUALIFIED, true);
        Bus::fake(SendDiscordNotificationJob::class);
        Event::fakeExcept(AnnouncementDeleted::class);

        $announcement->delete();

        Bus::assertDispatched(SendDiscordNotificationJob::class);
    }

    /**
     * When an announcement is restored, a SendDiscordNotification job shall be dispatched.
     *
     * @return void
     */
    public function testAnnouncementRestoredSendsDiscordNotification(): void
    {
        $announcement = Announcement::factory()->createOne();

        Config::set(FlagConstants::ALLOW_DISCORD_NOTIFICATIONS_FLAG_QUALIFIED, true);
        Bus::fake(SendDiscordNotificationJob::class);
        Event::fakeExcept(AnnouncementRestored::class);

        $announcement->restore();

        Bus::assertDispatched(SendDiscordNotificationJob::class);
    }

    /**
     * When an announcement is updated, a SendDiscordNotification job shall be dispatched.
     *
     * @return void
     */
    public function testAnnouncementUpdatedSendsDiscordNotification(): void
    {
        $announcement = Announcement::factory()->createOne();

        Config::set(FlagConstants::ALLOW_DISCORD_NOTIFICATIONS_FLAG_QUALIFIED, true);
        Bus::fake(SendDiscordNotificationJob::class);
        Event::fakeExcept(AnnouncementUpdated::class);

        $changes = Announcement::factory()->makeOne();

        $announcement->fill($changes->getAttributes());
        $announcement->save();

        Bus::assertDispatched(SendDiscordNotificationJob::class);
    }
}
