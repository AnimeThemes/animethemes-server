<?php

declare(strict_types=1);

namespace Tests\Feature\Jobs\Wiki;

use App\Constants\FeatureConstants;
use App\Events\Wiki\Video\VideoCreated;
use App\Events\Wiki\Video\VideoDeleted;
use App\Events\Wiki\Video\VideoRestored;
use App\Events\Wiki\Video\VideoUpdated;
use App\Jobs\SendDiscordNotificationJob;
use App\Models\Wiki\Video;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\Event;
use Laravel\Pennant\Feature;
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
    public function testVideoCreatedSendsDiscordNotification(): void
    {
        Feature::activate(FeatureConstants::ALLOW_DISCORD_NOTIFICATIONS);
        Bus::fake(SendDiscordNotificationJob::class);
        Event::fakeExcept(VideoCreated::class);

        Video::factory()->createOne();

        Bus::assertDispatched(SendDiscordNotificationJob::class);
    }

    /**
     * When a video is deleted, a SendDiscordNotification job shall be dispatched.
     *
     * @return void
     */
    public function testVideoDeletedSendsDiscordNotification(): void
    {
        $video = Video::factory()->createOne();

        Feature::activate(FeatureConstants::ALLOW_DISCORD_NOTIFICATIONS);
        Bus::fake(SendDiscordNotificationJob::class);
        Event::fakeExcept(VideoDeleted::class);

        $video->delete();

        Bus::assertDispatched(SendDiscordNotificationJob::class);
    }

    /**
     * When a video is restored, a SendDiscordNotification job shall be dispatched.
     *
     * @return void
     */
    public function testVideoRestoredSendsDiscordNotification(): void
    {
        $video = Video::factory()->createOne();

        Feature::activate(FeatureConstants::ALLOW_DISCORD_NOTIFICATIONS);
        Bus::fake(SendDiscordNotificationJob::class);
        Event::fakeExcept(VideoRestored::class);

        $video->restore();

        Bus::assertDispatched(SendDiscordNotificationJob::class);
    }

    /**
     * When a video is updated, a SendDiscordNotification job shall be dispatched.
     *
     * @return void
     */
    public function testVideoUpdatedSendsDiscordNotification(): void
    {
        $video = Video::factory()->createOne();

        Feature::activate(FeatureConstants::ALLOW_DISCORD_NOTIFICATIONS);
        Bus::fake(SendDiscordNotificationJob::class);
        Event::fakeExcept(VideoUpdated::class);

        $changes = Video::factory()->makeOne();

        $video->fill($changes->getAttributes());
        $video->save();

        Bus::assertDispatched(SendDiscordNotificationJob::class);
    }
}
