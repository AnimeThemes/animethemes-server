<?php

declare(strict_types=1);

use App\Events\Pivot\Wiki\SongResource\SongResourceCreated;
use App\Events\Pivot\Wiki\SongResource\SongResourceDeleted;
use App\Events\Pivot\Wiki\SongResource\SongResourceUpdated;
use App\Models\Wiki\ExternalResource;
use App\Models\Wiki\Song;
use App\Pivots\Wiki\SongResource;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Event;

test('song resource created event dispatched', function () {
    $song = Song::factory()->createOne();
    $resource = ExternalResource::factory()->createOne();

    $song->resources()->attach($resource);

    Event::assertDispatched(SongResourceCreated::class);
});

test('song resource deleted event dispatched', function () {
    $song = Song::factory()->createOne();
    $resource = ExternalResource::factory()->createOne();

    $song->resources()->attach($resource);
    $song->resources()->detach($resource);

    Event::assertDispatched(SongResourceDeleted::class);
});

test('song resource updated event dispatched', function () {
    $song = Song::factory()->createOne();
    $resource = ExternalResource::factory()->createOne();

    $songResource = SongResource::factory()
        ->for($song, 'song')
        ->for($resource, 'resource')
        ->createOne();

    $changes = SongResource::factory()
        ->for($song, 'song')
        ->for($resource, 'resource')
        ->makeOne();

    $songResource->fill($changes->getAttributes());
    $songResource->save();

    Event::assertDispatched(SongResourceUpdated::class);
});

test('song resource updated event embed fields', function () {
    $song = Song::factory()->createOne();
    $resource = ExternalResource::factory()->createOne();

    $songResource = SongResource::factory()
        ->for($song, 'song')
        ->for($resource, 'resource')
        ->createOne();

    $changes = SongResource::factory()
        ->for($song, 'song')
        ->for($resource, 'resource')
        ->makeOne();

    $songResource->fill($changes->getAttributes());
    $songResource->save();

    Event::assertDispatched(SongResourceUpdated::class, function (SongResourceUpdated $event) {
        $message = $event->getDiscordMessage();

        return ! empty(Arr::get($message->embed, 'fields'));
    });
});
