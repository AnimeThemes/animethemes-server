<?php

declare(strict_types=1);

namespace Tests\Feature\Events\List;

use App\Contracts\Models\HasHashids;
use App\Events\List\Playlist\PlaylistCreated;
use App\Events\List\Playlist\PlaylistDeleted;
use App\Events\List\Playlist\PlaylistUpdated;
use App\Models\Auth\User;
use App\Models\List\Playlist;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Event;
use Tests\TestCase;

class PlaylistTest extends TestCase
{
    /**
     * When a Playlist is created, a PlaylistCreated event shall be dispatched.
     */
    public function testPlaylistCreatedEventDispatched(): void
    {
        Playlist::factory()->createOne();

        Event::assertDispatched(PlaylistCreated::class);
    }

    /**
     * When a Playlist is deleted, a PlaylistDeleted event shall be dispatched.
     */
    public function testPlaylistDeletedEventDispatched(): void
    {
        $playlist = Playlist::factory()->createOne();

        $playlist->delete();

        Event::assertDispatched(PlaylistDeleted::class);
    }

    /**
     * When a Playlist is updated, a PlaylistUpdated event shall be dispatched.
     */
    public function testPlaylistUpdatedEventDispatched(): void
    {
        $playlist = Playlist::factory()->createOne();
        $changes = Playlist::factory()->makeOne();

        $playlist->fill($changes->getAttributes());
        $playlist->save();

        Event::assertDispatched(PlaylistUpdated::class);
    }

    /**
     * The PlaylistUpdated event shall contain embed fields.
     */
    public function testPlaylistUpdatedEventEmbedFields(): void
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
     */
    public function testPlaylistCreatedAssignsNullableUserHashids(): void
    {
        Event::fakeExcept(PlaylistCreated::class);

        Playlist::factory()->createOne();

        static::assertDatabaseMissing(Playlist::class, [HasHashids::ATTRIBUTE_HASHID => null]);
    }

    /**
     * The Playlist Created event shall assign hashids to the playlist with an owner.
     */
    public function testPlaylistCreatedAssignsNonNullUserHashids(): void
    {
        Event::fakeExcept(PlaylistCreated::class);

        Playlist::factory()
            ->for(User::factory())
            ->createOne();

        static::assertDatabaseMissing(Playlist::class, [HasHashids::ATTRIBUTE_HASHID => null]);
    }
}
