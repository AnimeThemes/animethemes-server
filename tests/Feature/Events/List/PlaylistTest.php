<?php

declare(strict_types=1);

use App\Contracts\Models\HasHashids;
use App\Events\List\Playlist\PlaylistCreated;
use App\Events\List\Playlist\PlaylistDeleted;
use App\Events\List\Playlist\PlaylistUpdated;
use App\Models\Auth\User;
use App\Models\List\Playlist;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Event;

test('playlist created event dispatched', function () {
    Playlist::factory()->createOne();

    Event::assertDispatched(PlaylistCreated::class);
});

test('playlist deleted event dispatched', function () {
    $playlist = Playlist::factory()->createOne();

    $playlist->delete();

    Event::assertDispatched(PlaylistDeleted::class);
});

test('playlist updated event dispatched', function () {
    $playlist = Playlist::factory()->createOne();
    $changes = Playlist::factory()->makeOne();

    $playlist->fill($changes->getAttributes());
    $playlist->save();

    Event::assertDispatched(PlaylistUpdated::class);
});

test('playlist updated event embed fields', function () {
    $playlist = Playlist::factory()->createOne();
    $changes = Playlist::factory()->makeOne();

    $playlist->fill($changes->getAttributes());
    $playlist->save();

    Event::assertDispatched(PlaylistUpdated::class, function (PlaylistUpdated $event) {
        $message = $event->getDiscordMessage();

        return ! empty(Arr::get($message->embed, 'fields'));
    });
});

test('playlist created assigns nullable user hashids', function () {
    Event::fakeExcept(PlaylistCreated::class);

    Playlist::factory()->createOne();

    $this->assertDatabaseMissing(Playlist::class, [HasHashids::ATTRIBUTE_HASHID => null]);
});

test('playlist created assigns non null user hashids', function () {
    Event::fakeExcept(PlaylistCreated::class);

    Playlist::factory()
        ->for(User::factory())
        ->createOne();

    $this->assertDatabaseMissing(Playlist::class, [HasHashids::ATTRIBUTE_HASHID => null]);
});
