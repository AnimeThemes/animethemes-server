<?php

declare(strict_types=1);

namespace Tests\Feature\Jobs\Admin;

use App\Constants\Config\FlagConstants;
use App\Jobs\SendDiscordNotificationJob;
use App\Models\Admin\Announcement;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\Config;
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
    public function testAnnouncementCreatedSendsDiscordNotification()
    {
        Config::set(FlagConstants::ALLOW_DISCORD_NOTIFICATIONS_FLAG_QUALIFIED, true);
        Bus::fake(SendDiscordNotificationJob::class);

        Announcement::factory()->createOne();

        Bus::assertDispatched(SendDiscordNotificationJob::class);
    }

    /**
     * When an announcement is deleted, a SendDiscordNotification job shall be dispatched.
     *
     * @return void
     */
    public function testAnnouncementDeletedSendsDiscordNotification()
    {
        $announcement = Announcement::factory()->createOne();

        Config::set(FlagConstants::ALLOW_DISCORD_NOTIFICATIONS_FLAG_QUALIFIED, true);
        Bus::fake(SendDiscordNotificationJob::class);

        $announcement->delete();

        Bus::assertDispatched(SendDiscordNotificationJob::class);
    }

    /**
     * When an announcement is restored, a SendDiscordNotification job shall be dispatched.
     *
     * @return void
     */
    public function testAnnouncementRestoredSendsDiscordNotification()
    {
        $announcement = Announcement::factory()->createOne();

        Config::set(FlagConstants::ALLOW_DISCORD_NOTIFICATIONS_FLAG_QUALIFIED, true);
        Bus::fake(SendDiscordNotificationJob::class);

        $announcement->restore();

        Bus::assertDispatched(SendDiscordNotificationJob::class);
    }

    /**
     * When an announcement is updated, a SendDiscordNotification job shall be dispatched.
     *
     * @return void
     */
    public function testAnnouncementUpdatedSendsDiscordNotification()
    {
        $announcement = Announcement::factory()->createOne();

        Config::set(FlagConstants::ALLOW_DISCORD_NOTIFICATIONS_FLAG_QUALIFIED, true);
        Bus::fake(SendDiscordNotificationJob::class);

        $changes = Announcement::factory()->makeOne();

        $announcement->fill($changes->getAttributes());
        $announcement->save();

        Bus::assertDispatched(SendDiscordNotificationJob::class);
    }
}
