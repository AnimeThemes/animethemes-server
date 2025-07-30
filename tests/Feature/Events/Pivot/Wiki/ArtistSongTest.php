<?php

declare(strict_types=1);

use App\Events\Pivot\Wiki\ArtistSong\ArtistSongCreated;
use App\Events\Pivot\Wiki\ArtistSong\ArtistSongDeleted;
use App\Events\Pivot\Wiki\ArtistSong\ArtistSongUpdated;
use App\Models\Wiki\Artist;
use App\Models\Wiki\Song;
use App\Pivots\Wiki\ArtistSong;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Event;

test('artist song created event dispatched', function () {
    $artist = Artist::factory()->createOne();
    $song = Song::factory()->createOne();

    $artist->songs()->attach($song);

    Event::assertDispatched(ArtistSongCreated::class);
});

test('artist song deleted event dispatched', function () {
    $artist = Artist::factory()->createOne();
    $song = Song::factory()->createOne();

    $artist->songs()->attach($song);
    $artist->songs()->detach($song);

    Event::assertDispatched(ArtistSongDeleted::class);
});

test('artist song updated event dispatched', function () {
    $artist = Artist::factory()->createOne();
    $song = Song::factory()->createOne();

    $artistSong = ArtistSong::factory()
        ->for($artist, 'artist')
        ->for($song, 'song')
        ->createOne();

    $changes = ArtistSong::factory()
        ->for($artist, 'artist')
        ->for($song, 'song')
        ->makeOne();

    $artistSong->fill($changes->getAttributes());
    $artistSong->save();

    Event::assertDispatched(ArtistSongUpdated::class);
});

test('artist song updated event embed fields', function () {
    $artist = Artist::factory()->createOne();
    $song = Song::factory()->createOne();

    $artistSong = ArtistSong::factory()
        ->for($artist, 'artist')
        ->for($song, 'song')
        ->createOne();

    $changes = ArtistSong::factory()
        ->for($artist, 'artist')
        ->for($song, 'song')
        ->makeOne();

    $artistSong->fill($changes->getAttributes());
    $artistSong->save();

    Event::assertDispatched(ArtistSongUpdated::class, function (ArtistSongUpdated $event) {
        $message = $event->getDiscordMessage();

        return ! empty(Arr::get($message->embed, 'fields'));
    });
});
