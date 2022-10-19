<?php

declare(strict_types=1);

namespace Tests\Feature\Jobs\List;

use App\Constants\Config\FlagConstants;
use App\Jobs\SendDiscordNotificationJob;
use App\Models\List\Playlist;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\Config;
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
        Config::set(FlagConstants::ALLOW_DISCORD_NOTIFICATIONS_FLAG_QUALIFIED, true);
        Bus::fake(SendDiscordNotificationJob::class);

        Playlist::factory()->createOne();

        Bus::assertDispatched(SendDiscordNotificationJob::class);
    }

    /**
     * When a playlist is deleted, a SendDiscordNotification job shall be dispatched.
     *
     * @return void
     */
    public function testPlaylistDeletedSendsDiscordNotification(): void
    {
        $playlist = Playlist::factory()->createOne();

        Config::set(FlagConstants::ALLOW_DISCORD_NOTIFICATIONS_FLAG_QUALIFIED, true);
        Bus::fake(SendDiscordNotificationJob::class);

        $playlist->delete();

        Bus::assertDispatched(SendDiscordNotificationJob::class);
    }

    /**
     * When a playlist is restored, a SendDiscordNotification job shall be dispatched.
     *
     * @return void
     */
    public function testPlaylistRestoredSendsDiscordNotification(): void
    {
        $playlist = Playlist::factory()->createOne();

        Config::set(FlagConstants::ALLOW_DISCORD_NOTIFICATIONS_FLAG_QUALIFIED, true);
        Bus::fake(SendDiscordNotificationJob::class);

        $playlist->restore();

        Bus::assertDispatched(SendDiscordNotificationJob::class);
    }

    /**
     * When a playlist is updated, a SendDiscordNotification job shall be dispatched.
     *
     * @return void
     */
    public function testPlaylistUpdatedSendsDiscordNotification(): void
    {
        $playlist = Playlist::factory()->createOne();

        Config::set(FlagConstants::ALLOW_DISCORD_NOTIFICATIONS_FLAG_QUALIFIED, true);
        Bus::fake(SendDiscordNotificationJob::class);

        $changes = Playlist::factory()->makeOne();

        $playlist->fill($changes->getAttributes());
        $playlist->save();

        Bus::assertDispatched(SendDiscordNotificationJob::class);
    }
}
