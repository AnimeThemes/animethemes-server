<?php

declare(strict_types=1);

namespace Tests\Feature\Events\List;

use App\Contracts\Models\HasHashids;
use App\Events\List\Playlist\PlaylistCreated;
use App\Events\List\Playlist\PlaylistDeleted;
use App\Events\List\Playlist\PlaylistRestored;
use App\Events\List\Playlist\PlaylistUpdated;
use App\Models\Auth\User;
use App\Models\List\Playlist;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Event;
use Tests\TestCase;

/**
 * Class PlaylistTest.
 */
class PlaylistTest extends TestCase
{
    /**
     * When a Playlist is created, a PlaylistCreated event shall be dispatched.
     *
     * @return void
     */
    public function test_playlist_created_event_dispatched(): void
    {
        Playlist::factory()->createOne();

        Event::assertDispatched(PlaylistCreated::class);
    }

    /**
     * When a Playlist is deleted, a PlaylistDeleted event shall be dispatched.
     *
     * @return void
     */
    public function test_playlist_deleted_event_dispatched(): void
    {
        $playlist = Playlist::factory()->createOne();

        $playlist->delete();

        Event::assertDispatched(PlaylistDeleted::class);
    }

    /**
     * When a Playlist is restored, a PlaylistRestored event shall be dispatched.
     *
     * @return void
     */
    public function test_playlist_restored_event_dispatched(): void
    {
        $playlist = Playlist::factory()->createOne();

        $playlist->restore();

        Event::assertDispatched(PlaylistRestored::class);
    }

    /**
     * When a Playlist is restored, a PlaylistUpdated event shall not be dispatched.
     * Note: This is a customization that overrides default framework behavior.
     * An updated event is fired on restore.
     *
     * @return void
     */
    public function test_playlist_restores_quietly(): void
    {
        $playlist = Playlist::factory()->createOne();

        $playlist->restore();

        Event::assertNotDispatched(PlaylistUpdated::class);
    }

    /**
     * When a Playlist is updated, a PlaylistUpdated event shall be dispatched.
     *
     * @return void
     */
    public function test_playlist_updated_event_dispatched(): void
    {
        $playlist = Playlist::factory()->createOne();
        $changes = Playlist::factory()->makeOne();

        $playlist->fill($changes->getAttributes());
        $playlist->save();

        Event::assertDispatched(PlaylistUpdated::class);
    }

    /**
     * The PlaylistUpdated event shall contain embed fields.
     *
     * @return void
     */
    public function test_playlist_updated_event_embed_fields(): void
    {
        $playlist = Playlist::factory()->createOne();
        $changes = Playlist::factory()->makeOne();

        $playlist->fill($changes->getAttributes());
        $playlist->save();

        Event::assertDispatched(PlaylistUpdated::class, function (PlaylistUpdated $event) {
            $message = $event->getDiscordMessage();

            return ! empty(Arr::get($message->embed, 'fields'));
        });
    }

    /**
     * The Playlist Created event shall assign hashids to the playlist without an owner.
     *
     * @return void
     */
    public function test_playlist_created_assigns_nullable_user_hashids(): void
    {
        Event::fakeExcept(PlaylistCreated::class);

        Playlist::factory()->createOne();

        static::assertDatabaseMissing(Playlist::class, [HasHashids::ATTRIBUTE_HASHID => null]);
    }

    /**
     * The Playlist Created event shall assign hashids to the playlist with an owner.
     *
     * @return void
     */
    public function test_playlist_created_assigns_non_null_user_hashids(): void
    {
        Event::fakeExcept(PlaylistCreated::class);

        Playlist::factory()
            ->for(User::factory())
            ->createOne();

        static::assertDatabaseMissing(Playlist::class, [HasHashids::ATTRIBUTE_HASHID => null]);
    }
}
