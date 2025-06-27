<?php

declare(strict_types=1);

namespace Tests\Feature\Jobs\Admin;

use App\Constants\FeatureConstants;
use App\Events\Admin\Announcement\AnnouncementCreated;
use App\Events\Admin\Announcement\AnnouncementDeleted;
use App\Events\Admin\Announcement\AnnouncementRestored;
use App\Events\Admin\Announcement\AnnouncementUpdated;
use App\Jobs\SendDiscordNotificationJob;
use App\Models\Admin\Announcement;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\Event;
use Laravel\Pennant\Feature;
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
    public function test_announcement_created_sends_discord_notification(): void
    {
        Feature::activate(FeatureConstants::ALLOW_DISCORD_NOTIFICATIONS);
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
    public function test_announcement_deleted_sends_discord_notification(): void
    {
        $announcement = Announcement::factory()->createOne();

        Feature::activate(FeatureConstants::ALLOW_DISCORD_NOTIFICATIONS);
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
    public function test_announcement_restored_sends_discord_notification(): void
    {
        $announcement = Announcement::factory()->createOne();

        Feature::activate(FeatureConstants::ALLOW_DISCORD_NOTIFICATIONS);
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
    public function test_announcement_updated_sends_discord_notification(): void
    {
        $announcement = Announcement::factory()->createOne();

        Feature::activate(FeatureConstants::ALLOW_DISCORD_NOTIFICATIONS);
        Bus::fake(SendDiscordNotificationJob::class);
        Event::fakeExcept(AnnouncementUpdated::class);

        $changes = Announcement::factory()->makeOne();

        $announcement->fill($changes->getAttributes());
        $announcement->save();

        Bus::assertDispatched(SendDiscordNotificationJob::class);
    }
}
