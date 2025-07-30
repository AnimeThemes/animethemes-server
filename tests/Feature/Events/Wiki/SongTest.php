<?php

declare(strict_types=1);

use App\Events\Wiki\Song\SongCreated;
use App\Events\Wiki\Song\SongDeleted;
use App\Events\Wiki\Song\SongRestored;
use App\Events\Wiki\Song\SongUpdated;
use App\Models\Wiki\Song;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Event;

test('song created event dispatched', function () {
    Song::factory()->createOne();

    Event::assertDispatched(SongCreated::class);
});

test('song deleted event dispatched', function () {
    $song = Song::factory()->createOne();

    $song->delete();

    Event::assertDispatched(SongDeleted::class);
});

test('song restored event dispatched', function () {
    $song = Song::factory()->createOne();

    $song->restore();

    Event::assertDispatched(SongRestored::class);
});

test('song restores quietly', function () {
    $song = Song::factory()->createOne();

    $song->restore();

    Event::assertNotDispatched(SongUpdated::class);
});

test('song updated event dispatched', function () {
    $song = Song::factory()->createOne();
    $changes = Song::factory()->makeOne();

    $song->fill($changes->getAttributes());
    $song->save();

    Event::assertDispatched(SongUpdated::class);
});

test('song updated event embed fields', function () {
    $song = Song::factory()->createOne();
    $changes = Song::factory()->makeOne();

    $song->fill($changes->getAttributes());
    $song->save();

    Event::assertDispatched(SongUpdated::class, function (SongUpdated $event) {
        $message = $event->getDiscordMessage();

        return ! empty(Arr::get($message->embed, 'fields'));
    });
});
