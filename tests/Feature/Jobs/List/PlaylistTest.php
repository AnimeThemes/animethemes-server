<?php

declare(strict_types=1);

namespace Tests\Feature\Jobs\List;

use App\Constants\FeatureConstants;
use App\Events\List\Playlist\PlaylistCreated;
use App\Events\List\Playlist\PlaylistDeleted;
use App\Events\List\Playlist\PlaylistRestored;
use App\Events\List\Playlist\PlaylistUpdated;
use App\Jobs\SendDiscordNotificationJob;
use App\Models\List\Playlist;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\Event;
use Laravel\Pennant\Feature;
use Tests\TestCase;

/**
 * Class PlaylistTest.
 */
class PlaylistTest extends TestCase
{
    /**
     * When a playlist is created, a SendDiscordNotification job shall be dispatched.
     *
     * @return void
     */
    public function testPlaylistCreatedSendsDiscordNotification(): void
    {
        Feature::activate(FeatureConstants::ALLOW_DISCORD_NOTIFICATIONS);
        Bus::fake(SendDiscordNotificationJob::class);
        Event::fakeExcept(PlaylistCreated::class);

        Playlist::factory()->createOne();

        Bus::assertDispatched(SendDiscordNotificationJob::class);
    }

    /**
     * When a playlist is deleted, a SendDiscordNotification job shall not be dispatched.
     *
     * @return void
     */
    public function testPlaylistDeletedSendsDiscordNotification(): void
    {
        $playlist = Playlist::factory()->createOne();

        Feature::activate(FeatureConstants::ALLOW_DISCORD_NOTIFICATIONS);
        Bus::fake(SendDiscordNotificationJob::class);
        Event::fakeExcept(PlaylistDeleted::class);

        $playlist->delete();

        Bus::assertNotDispatched(SendDiscordNotificationJob::class);
    }

    /**
     * When a playlist is restored, a SendDiscordNotification job shall not be dispatched.
     *
     * @return void
     */
    public function testPlaylistRestoredSendsDiscordNotification(): void
    {
        $playlist = Playlist::factory()->createOne();

        Feature::activate(FeatureConstants::ALLOW_DISCORD_NOTIFICATIONS);
        Bus::fake(SendDiscordNotificationJob::class);
        Event::fakeExcept(PlaylistRestored::class);

        $playlist->restore();

        Bus::assertNotDispatched(SendDiscordNotificationJob::class);
    }

    /**
     * When a playlist is updated, a SendDiscordNotification job shall be dispatched.
     *
     * @return void
     */
    public function testPlaylistUpdatedSendsDiscordNotification(): void
    {
        $playlist = Playlist::factory()->createOne();

        Feature::activate(FeatureConstants::ALLOW_DISCORD_NOTIFICATIONS);
        Bus::fake(SendDiscordNotificationJob::class);
        Event::fakeExcept(PlaylistUpdated::class);

        $changes = Playlist::factory()->makeOne();

        $playlist->fill($changes->getAttributes());
        $playlist->save();

        Bus::assertDispatched(SendDiscordNotificationJob::class);
    }
}
