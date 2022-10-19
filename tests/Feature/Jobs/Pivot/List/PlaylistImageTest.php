<?php

declare(strict_types=1);

namespace Tests\Feature\Jobs\Pivot\List;

use App\Constants\Config\FlagConstants;
use App\Jobs\SendDiscordNotificationJob;
use App\Models\List\Playlist;
use App\Models\Wiki\Image;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\Config;
use Tests\TestCase;

/**
 * Class PlaylistImageTest.
 */
class PlaylistImageTest extends TestCase
{
    /**
     * When a Playlist is attached to an Image or vice versa, a SendDiscordNotification job shall be dispatched.
     *
     * @return void
     */
    public function testPlaylistImageCreatedSendsDiscordNotification(): void
    {
        $playlist = Playlist::factory()->createOne();
        $image = Image::factory()->createOne();

        Config::set(FlagConstants::ALLOW_DISCORD_NOTIFICATIONS_FLAG_QUALIFIED, true);
        Bus::fake(SendDiscordNotificationJob::class);

        $playlist->images()->attach($image);

        Bus::assertDispatched(SendDiscordNotificationJob::class);
    }

    /**
     * When a Playlist is detached from an Image or vice versa, a SendDiscordNotification job shall be dispatched.
     *
     * @return void
     */
    public function testPlaylistImageDeletedSendsDiscordNotification(): void
    {
        $playlist = Playlist::factory()->createOne();
        $image = Image::factory()->createOne();

        $playlist->images()->attach($image);

        Config::set(FlagConstants::ALLOW_DISCORD_NOTIFICATIONS_FLAG_QUALIFIED, true);
        Bus::fake(SendDiscordNotificationJob::class);

        $playlist->images()->detach($image);

        Bus::assertDispatched(SendDiscordNotificationJob::class);
    }
}
