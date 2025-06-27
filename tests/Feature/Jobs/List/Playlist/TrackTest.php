<?php

declare(strict_types=1);

namespace Tests\Feature\Jobs\List\Playlist;

use App\Constants\FeatureConstants;
use App\Events\List\Playlist\Track\TrackCreated;
use App\Events\List\Playlist\Track\TrackDeleted;
use App\Events\List\Playlist\Track\TrackRestored;
use App\Events\List\Playlist\Track\TrackUpdated;
use App\Jobs\SendDiscordNotificationJob;
use App\Models\List\Playlist;
use App\Models\List\Playlist\PlaylistTrack;
use App\Models\Wiki\Video;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\Event;
use Laravel\Pennant\Feature;
use Tests\TestCase;

/**
 * Class TrackTest.
 */
class TrackTest extends TestCase
{
    /**
     * When a track is created, a SendDiscordNotification job shall not be dispatched.
     *
     * @return void
     */
    public function test_playlist_created_sends_discord_notification(): void
    {
        $playlist = Playlist::factory()->createOne();

        Feature::activate(FeatureConstants::ALLOW_DISCORD_NOTIFICATIONS);
        Bus::fake(SendDiscordNotificationJob::class);
        Event::fakeExcept(TrackCreated::class);

        PlaylistTrack::factory()
            ->for($playlist)
            ->createOne();

        Bus::assertNotDispatched(SendDiscordNotificationJob::class);
    }

    /**
     * When a track is deleted, a SendDiscordNotification job shall not be dispatched.
     *
     * @return void
     */
    public function test_playlist_deleted_sends_discord_notification(): void
    {
        $playlist = Playlist::factory()->createOne();

        $track = PlaylistTrack::factory()
            ->for($playlist)
            ->createOne();

        Feature::activate(FeatureConstants::ALLOW_DISCORD_NOTIFICATIONS);
        Bus::fake(SendDiscordNotificationJob::class);
        Event::fakeExcept(TrackDeleted::class);

        $track->delete();

        Bus::assertNotDispatched(SendDiscordNotificationJob::class);
    }

    /**
     * When a track is restored, a SendDiscordNotification job shall not be dispatched.
     *
     * @return void
     */
    public function test_playlist_restored_sends_discord_notification(): void
    {
        $playlist = Playlist::factory()->createOne();

        $track = PlaylistTrack::factory()
            ->for($playlist)
            ->createOne();

        Feature::activate(FeatureConstants::ALLOW_DISCORD_NOTIFICATIONS);
        Bus::fake(SendDiscordNotificationJob::class);
        Event::fakeExcept(TrackRestored::class);

        $track->restore();

        Bus::assertNotDispatched(SendDiscordNotificationJob::class);
    }

    /**
     * When a track is updated, a SendDiscordNotification job shall not be dispatched.
     *
     * @return void
     */
    public function test_playlist_updated_sends_discord_notification(): void
    {
        $playlist = Playlist::factory()->createOne();

        $track = PlaylistTrack::factory()
            ->for($playlist)
            ->createOne();

        Feature::activate(FeatureConstants::ALLOW_DISCORD_NOTIFICATIONS);
        Bus::fake(SendDiscordNotificationJob::class);
        Event::fakeExcept(TrackUpdated::class);

        $changes = array_merge(
            PlaylistTrack::factory()->raw(),
            [PlaylistTrack::ATTRIBUTE_VIDEO => Video::factory()->createOne()->getKey()],
        );

        $track->fill($changes);
        $track->save();

        Bus::assertNotDispatched(SendDiscordNotificationJob::class);
    }
}
