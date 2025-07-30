<?php

declare(strict_types=1);

use App\Contracts\Models\HasHashids;
use App\Events\List\Playlist\Track\TrackCreated;
use App\Events\List\Playlist\Track\TrackDeleted;
use App\Events\List\Playlist\Track\TrackUpdated;
use App\Models\List\Playlist;
use App\Models\List\Playlist\PlaylistTrack;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Event;

test('track created event dispatched', function () {
    PlaylistTrack::factory()
        ->for(Playlist::factory())
        ->createOne();

    Event::assertDispatched(TrackCreated::class);
});

test('track deleted event dispatched', function () {
    $track = PlaylistTrack::factory()
        ->for(Playlist::factory())
        ->createOne();

    $track->delete();

    Event::assertDispatched(TrackDeleted::class);
});

test('track updated event dispatched', function () {
    $track = PlaylistTrack::factory()
        ->for(Playlist::factory())
        ->createOne();

    $changes = PlaylistTrack::factory()
        ->for(Playlist::factory())
        ->makeOne();

    $track->fill($changes->getAttributes());
    $track->save();

    Event::assertDispatched(TrackUpdated::class);
});

test('playlist updated event embed fields', function () {
    $track = PlaylistTrack::factory()
        ->for(Playlist::factory())
        ->createOne();

    $changes = PlaylistTrack::factory()
        ->for(Playlist::factory())
        ->makeOne();

    $track->fill($changes->getAttributes());
    $track->save();

    Event::assertDispatched(TrackUpdated::class, function (TrackUpdated $event) {
        $message = $event->getDiscordMessage();

        return ! empty(Arr::get($message->embed, 'fields'));
    });
});

test('playlist created assigns hashids', function () {
    Event::fakeExcept(TrackCreated::class);

    PlaylistTrack::factory()
        ->for(Playlist::factory())
        ->createOne();

    static::assertDatabaseMissing(PlaylistTrack::class, [HasHashids::ATTRIBUTE_HASHID => null]);
});
