<?php declare(strict_types=1);

namespace Jobs;

use App\Jobs\SendDiscordNotification;
use App\Models\Announcement;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\Config;
use Tests\TestCase;

/**
 * Class AnnouncementTest
 * @package Jobs
 */
class AnnouncementTest extends TestCase
{
    use RefreshDatabase;

    /**
     * When an announcement is created, a SendDiscordNotification job shall be dispatched.
     *
     * @return void
     */
    public function testAnnouncementCreatedSendsDiscordNotification()
    {
        Config::set('app.allow_discord_notifications', true);
        Bus::fake(SendDiscordNotification::class);

        Announcement::factory()->create();

        Bus::assertDispatched(SendDiscordNotification::class);
    }

    /**
     * When an announcement is deleted, a SendDiscordNotification job shall be dispatched.
     *
     * @return void
     */
    public function testAnnouncementDeletedSendsDiscordNotification()
    {
        $announcement = Announcement::factory()->create();

        Config::set('app.allow_discord_notifications', true);
        Bus::fake(SendDiscordNotification::class);

        $announcement->delete();

        Bus::assertDispatched(SendDiscordNotification::class);
    }

    /**
     * When an announcement is restored, a SendDiscordNotification job shall be dispatched.
     *
     * @return void
     */
    public function testAnnouncementRestoredSendsDiscordNotification()
    {
        $announcement = Announcement::factory()->create();

        Config::set('app.allow_discord_notifications', true);
        Bus::fake(SendDiscordNotification::class);

        $announcement->restore();

        Bus::assertDispatched(SendDiscordNotification::class);
    }

    /**
     * When an announcement is updated, a SendDiscordNotification job shall be dispatched.
     *
     * @return void
     */
    public function testAnnouncementUpdatedSendsDiscordNotification()
    {
        $announcement = Announcement::factory()->create();

        Config::set('app.allow_discord_notifications', true);
        Bus::fake(SendDiscordNotification::class);

        $changes = Announcement::factory()->make();

        $announcement->fill($changes->getAttributes());
        $announcement->save();

        Bus::assertDispatched(SendDiscordNotification::class);
    }
}
