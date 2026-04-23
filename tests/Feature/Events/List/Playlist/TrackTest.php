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

test('track created event dispatched', function (): void {
    PlaylistTrack::factory()
        ->for(Playlist::factory())
        ->createOne();

    Event::assertDispatched(TrackCreated::class);
});

test('track deleted event dispatched', function (): void {
    $track = PlaylistTrack::factory()
        ->for(Playlist::factory())
        ->createOne();

    $track->delete();

    Event::assertDispatched(TrackDeleted::class);
});

test('track updated event dispatched', function (): void {
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

test('playlist updated event embed fields', function (): void {
    $track = PlaylistTrack::factory()
        ->for(Playlist::factory())
        ->createOne();

    $changes = PlaylistTrack::factory()
        ->for(Playlist::factory())
        ->makeOne();

    $track->fill($changes->getAttributes());
    $track->save();

    Event::assertDispatched(TrackUpdated::class, function (TrackUpdated $event): bool {
        $message = $event->getDiscordMessage();

        return filled(Arr::get($message->embed, 'fields'));
    });
});

test('playlist created assigns hashids', function (): void {
    Event::fakeExcept(TrackCreated::class);

    PlaylistTrack::factory()
        ->for(Playlist::factory())
        ->createOne();

    $this->assertDatabaseMissing(PlaylistTrack::class, [HasHashids::ATTRIBUTE_HASHID => null]);
});
