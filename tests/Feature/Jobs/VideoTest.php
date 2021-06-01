<?php declare(strict_types=1);

namespace Jobs;

use App\Jobs\SendDiscordNotification;
use App\Models\Video;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\Config;
use Tests\TestCase;

/**
 * Class VideoTest
 * @package Jobs
 */
class VideoTest extends TestCase
{
    use RefreshDatabase;

    /**
     * When a video is created, a SendDiscordNotification job shall be dispatched.
     *
     * @return void
     */
    public function testVideoCreatedSendsDiscordNotification()
    {
        Config::set('app.allow_discord_notifications', true);
        Bus::fake(SendDiscordNotification::class);

        Video::factory()->create();

        Bus::assertDispatched(SendDiscordNotification::class);
    }

    /**
     * When a video is deleted, a SendDiscordNotification job shall be dispatched.
     *
     * @return void
     */
    public function testVideoDeletedSendsDiscordNotification()
    {
        $video = Video::factory()->create();

        Config::set('app.allow_discord_notifications', true);
        Bus::fake(SendDiscordNotification::class);

        $video->delete();

        Bus::assertDispatched(SendDiscordNotification::class);
    }

    /**
     * When a video is restored, a SendDiscordNotification job shall be dispatched.
     *
     * @return void
     */
    public function testVideoRestoredSendsDiscordNotification()
    {
        $video = Video::factory()->create();

        Config::set('app.allow_discord_notifications', true);
        Bus::fake(SendDiscordNotification::class);

        $video->restore();

        Bus::assertDispatched(SendDiscordNotification::class);
    }

    /**
     * When a video is updated, a SendDiscordNotification job shall be dispatched.
     *
     * @return void
     */
    public function testVideoUpdatedSendsDiscordNotification()
    {
        $video = Video::factory()->create();

        Config::set('app.allow_discord_notifications', true);
        Bus::fake(SendDiscordNotification::class);

        $changes = Video::factory()->make();

        $video->fill($changes->getAttributes());
        $video->save();

        Bus::assertDispatched(SendDiscordNotification::class);
    }
}
