<?php

declare(strict_types=1);

namespace Tests\Feature\Jobs\Wiki;

use App\Constants\FeatureConstants;
use App\Events\Wiki\Song\SongCreated;
use App\Events\Wiki\Song\SongDeleted;
use App\Events\Wiki\Song\SongRestored;
use App\Events\Wiki\Song\SongUpdated;
use App\Jobs\SendDiscordNotificationJob;
use App\Models\Wiki\Song;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\Event;
use Laravel\Pennant\Feature;
use Tests\TestCase;

/**
 * Class SongTest.
 */
class SongTest extends TestCase
{
    /**
     * When a song is created, a SendDiscordNotification job shall be dispatched.
     *
     * @return void
     */
    public function testSongCreatedSendsDiscordNotification(): void
    {
        Feature::activate(FeatureConstants::ALLOW_DISCORD_NOTIFICATIONS);
        Bus::fake(SendDiscordNotificationJob::class);
        Event::fakeExcept(SongCreated::class);

        Song::factory()->createOne();

        Bus::assertDispatched(SendDiscordNotificationJob::class);
    }

    /**
     * When a song is deleted, a SendDiscordNotification job shall be dispatched.
     *
     * @return void
     */
    public function testSongDeletedSendsDiscordNotification(): void
    {
        $song = Song::factory()->createOne();

        Feature::activate(FeatureConstants::ALLOW_DISCORD_NOTIFICATIONS);
        Bus::fake(SendDiscordNotificationJob::class);
        Event::fakeExcept(SongDeleted::class);

        $song->delete();

        Bus::assertDispatched(SendDiscordNotificationJob::class);
    }

    /**
     * When a song is restored, a SendDiscordNotification job shall be dispatched.
     *
     * @return void
     */
    public function testSongRestoredSendsDiscordNotification(): void
    {
        $song = Song::factory()->createOne();

        Feature::activate(FeatureConstants::ALLOW_DISCORD_NOTIFICATIONS);
        Bus::fake(SendDiscordNotificationJob::class);
        Event::fakeExcept(SongRestored::class);

        $song->restore();

        Bus::assertDispatched(SendDiscordNotificationJob::class);
    }

    /**
     * When a song is updated, a SendDiscordNotification job shall be dispatched.
     *
     * @return void
     */
    public function testSongUpdatedSendsDiscordNotification(): void
    {
        $song = Song::factory()->createOne();

        Feature::activate(FeatureConstants::ALLOW_DISCORD_NOTIFICATIONS);
        Bus::fake(SendDiscordNotificationJob::class);
        Event::fakeExcept(SongUpdated::class);

        $changes = Song::factory()->makeOne();

        $song->fill($changes->getAttributes());
        $song->save();

        Bus::assertDispatched(SendDiscordNotificationJob::class);
    }
}
