<?php

declare(strict_types=1);

namespace Tests\Feature\Jobs\Wiki;

use App\Jobs\SendDiscordNotificationJob;
use App\Models\Wiki\Video;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\Config;
use Tests\TestCase;

/**
 * Class VideoTest.
 */
class VideoTest extends TestCase
{

    /**
     * When a video is created, a SendDiscordNotification job shall be dispatched.
     *
     * @return void
     */
    public function testVideoCreatedSendsDiscordNotification()
    {
        Config::set('flags.allow_discord_notifications', true);
        Bus::fake(SendDiscordNotificationJob::class);

        Video::factory()->createOne();

        Bus::assertDispatched(SendDiscordNotificationJob::class);
    }

    /**
     * When a video is deleted, a SendDiscordNotification job shall be dispatched.
     *
     * @return void
     */
    public function testVideoDeletedSendsDiscordNotification()
    {
        $video = Video::factory()->createOne();

        Config::set('flags.allow_discord_notifications', true);
        Bus::fake(SendDiscordNotificationJob::class);

        $video->delete();

        Bus::assertDispatched(SendDiscordNotificationJob::class);
    }

    /**
     * When a video is restored, a SendDiscordNotification job shall be dispatched.
     *
     * @return void
     */
    public function testVideoRestoredSendsDiscordNotification()
    {
        $video = Video::factory()->createOne();

        Config::set('flags.allow_discord_notifications', true);
        Bus::fake(SendDiscordNotificationJob::class);

        $video->restore();

        Bus::assertDispatched(SendDiscordNotificationJob::class);
    }

    /**
     * When a video is updated, a SendDiscordNotification job shall be dispatched.
     *
     * @return void
     */
    public function testVideoUpdatedSendsDiscordNotification()
    {
        $video = Video::factory()->createOne();

        Config::set('flags.allow_discord_notifications', true);
        Bus::fake(SendDiscordNotificationJob::class);

        $changes = Video::factory()->makeOne();

        $video->fill($changes->getAttributes());
        $video->save();

        Bus::assertDispatched(SendDiscordNotificationJob::class);
    }
}
