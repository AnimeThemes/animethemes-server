<?php

declare(strict_types=1);

namespace Tests\Feature\Events\Pivot\List;

use App\Events\Pivot\List\PlaylistImage\PlaylistImageCreated;
use App\Events\Pivot\List\PlaylistImage\PlaylistImageDeleted;
use App\Models\List\Playlist;
use App\Models\Wiki\Image;
use Illuminate\Support\Facades\Event;
use Tests\TestCase;

/**
 * Class PlaylistImageTest.
 */
class PlaylistImageTest extends TestCase
{
    /**
     * When a Playlist is attached to an Image or vice versa, a PlaylistImageCreated event shall be dispatched.
     *
     * @return void
     */
    public function testPlaylistImageCreatedEventDispatched(): void
    {
        Event::fake();

        $playlist = Playlist::factory()->createOne();
        $image = Image::factory()->createOne();

        $playlist->images()->attach($image);

        Event::assertDispatched(PlaylistImageCreated::class);
    }

    /**
     * When a Playlist is detached from an Image or vice versa, a PlaylistImageDeleted event shall be dispatched.
     *
     * @return void
     */
    public function testPlaylistImageDeletedEventDispatched(): void
    {
        Event::fake();

        $playlist = Playlist::factory()->createOne();
        $image = Image::factory()->createOne();

        $playlist->images()->attach($image);
        $playlist->images()->detach($image);

        Event::assertDispatched(PlaylistImageDeleted::class);
    }
}
