<?php

declare(strict_types=1);

namespace Tests\Feature\Jobs\Pivot\List;

use App\Constants\FeatureConstants;
use App\Events\Pivot\List\PlaylistImage\PlaylistImageCreated;
use App\Events\Pivot\List\PlaylistImage\PlaylistImageDeleted;
use App\Jobs\SendDiscordNotificationJob;
use App\Models\List\Playlist;
use App\Models\Wiki\Image;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\Event;
use Laravel\Pennant\Feature;
use Tests\TestCase;

class PlaylistImageTest extends TestCase
{
    /**
     * When a Playlist is attached to an Image or vice versa, a SendDiscordNotification job shall be dispatched.
     */
    public function testPlaylistImageCreatedSendsDiscordNotification(): void
    {
        $playlist = Playlist::factory()->createOne();
        $image = Image::factory()->createOne();

        Feature::activate(FeatureConstants::ALLOW_DISCORD_NOTIFICATIONS);
        Bus::fake(SendDiscordNotificationJob::class);
        Event::fakeExcept(PlaylistImageCreated::class);

        $playlist->images()->attach($image);

        Bus::assertDispatched(SendDiscordNotificationJob::class);
    }

    /**
     * When a Playlist is detached from an Image or vice versa, a SendDiscordNotification job shall be dispatched.
     */
    public function testPlaylistImageDeletedSendsDiscordNotification(): void
    {
        $playlist = Playlist::factory()->createOne();
        $image = Image::factory()->createOne();

        $playlist->images()->attach($image);

        Feature::activate(FeatureConstants::ALLOW_DISCORD_NOTIFICATIONS);
        Bus::fake(SendDiscordNotificationJob::class);
        Event::fakeExcept(PlaylistImageDeleted::class);

        $playlist->images()->detach($image);

        Bus::assertDispatched(SendDiscordNotificationJob::class);
    }
}
