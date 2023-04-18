<?php

declare(strict_types=1);

namespace Tests\Feature\Jobs\List\Playlist;

use App\Constants\Config\FlagConstants;
use App\Events\List\Playlist\Track\TrackCreated;
use App\Events\List\Playlist\Track\TrackDeleted;
use App\Events\List\Playlist\Track\TrackRestored;
use App\Events\List\Playlist\Track\TrackUpdated;
use App\Jobs\SendDiscordNotificationJob;
use App\Models\List\Playlist;
use App\Models\List\Playlist\PlaylistTrack;
use App\Models\Wiki\Video;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Event;
use Tests\TestCase;

/**
 * Class TrackTest.
 */
class TrackTest extends TestCase
{
    /**
     * When a track is created, a SendDiscordNotification job shall be dispatched.
     *
     * @return void
     */
    public function testPlaylistCreatedSendsDiscordNotification(): void
    {
        $playlist = Playlist::factory()->createOne();

        Config::set(FlagConstants::ALLOW_DISCORD_NOTIFICATIONS_FLAG_QUALIFIED, true);
        Bus::fake(SendDiscordNotificationJob::class);
        Event::fakeExcept(TrackCreated::class);

        PlaylistTrack::factory()
            ->for($playlist)
            ->createOne();

        Bus::assertDispatched(SendDiscordNotificationJob::class);
    }

    /**
     * When a track is deleted, a SendDiscordNotification job shall be dispatched.
     *
     * @return void
     */
    public function testPlaylistDeletedSendsDiscordNotification(): void
    {
        $playlist = Playlist::factory()->createOne();

        $track = PlaylistTrack::factory()
            ->for($playlist)
            ->createOne();

        Config::set(FlagConstants::ALLOW_DISCORD_NOTIFICATIONS_FLAG_QUALIFIED, true);
        Bus::fake(SendDiscordNotificationJob::class);
        Event::fakeExcept(TrackDeleted::class);

        $track->delete();

        Bus::assertDispatched(SendDiscordNotificationJob::class);
    }

    /**
     * When a track is restored, a SendDiscordNotification job shall be dispatched.
     *
     * @return void
     */
    public function testPlaylistRestoredSendsDiscordNotification(): void
    {
        $playlist = Playlist::factory()->createOne();

        $track = PlaylistTrack::factory()
            ->for($playlist)
            ->createOne();

        Config::set(FlagConstants::ALLOW_DISCORD_NOTIFICATIONS_FLAG_QUALIFIED, true);
        Bus::fake(SendDiscordNotificationJob::class);
        Event::fakeExcept(TrackRestored::class);

        $track->restore();

        Bus::assertDispatched(SendDiscordNotificationJob::class);
    }

    /**
     * When a track is updated, a SendDiscordNotification job shall be dispatched.
     *
     * @return void
     */
    public function testPlaylistUpdatedSendsDiscordNotification(): void
    {
        $playlist = Playlist::factory()->createOne();

        $track = PlaylistTrack::factory()
            ->for($playlist)
            ->createOne();

        Config::set(FlagConstants::ALLOW_DISCORD_NOTIFICATIONS_FLAG_QUALIFIED, true);
        Bus::fake(SendDiscordNotificationJob::class);
        Event::fakeExcept(TrackUpdated::class);

        $changes = array_merge(
            PlaylistTrack::factory()->raw(),
            [PlaylistTrack::ATTRIBUTE_VIDEO => Video::factory()->createOne()->getKey()],
        );

        $track->fill($changes);
        $track->save();

        Bus::assertDispatched(SendDiscordNotificationJob::class);
    }
}
