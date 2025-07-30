<?php

declare(strict_types=1);

use App\Events\Pivot\Wiki\ArtistResource\ArtistResourceCreated;
use App\Events\Pivot\Wiki\ArtistResource\ArtistResourceDeleted;
use App\Events\Pivot\Wiki\ArtistResource\ArtistResourceUpdated;
use App\Models\Wiki\Artist;
use App\Models\Wiki\ExternalResource;
use App\Pivots\Wiki\ArtistResource;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Event;

test('artist resource created event dispatched', function () {
    $artist = Artist::factory()->createOne();
    $resource = ExternalResource::factory()->createOne();

    $artist->resources()->attach($resource);

    Event::assertDispatched(ArtistResourceCreated::class);
});

test('artist resource deleted event dispatched', function () {
    $artist = Artist::factory()->createOne();
    $resource = ExternalResource::factory()->createOne();

    $artist->resources()->attach($resource);
    $artist->resources()->detach($resource);

    Event::assertDispatched(ArtistResourceDeleted::class);
});

test('artist resource updated event dispatched', function () {
    $artist = Artist::factory()->createOne();
    $resource = ExternalResource::factory()->createOne();

    $artistResource = ArtistResource::factory()
        ->for($artist, 'artist')
        ->for($resource, 'resource')
        ->createOne();

    $changes = ArtistResource::factory()
        ->for($artist, 'artist')
        ->for($resource, 'resource')
        ->makeOne();

    $artistResource->fill($changes->getAttributes());
    $artistResource->save();

    Event::assertDispatched(ArtistResourceUpdated::class);
});

test('artist resource updated event embed fields', function () {
    $artist = Artist::factory()->createOne();
    $resource = ExternalResource::factory()->createOne();

    $artistResource = ArtistResource::factory()
        ->for($artist, 'artist')
        ->for($resource, 'resource')
        ->createOne();

    $changes = ArtistResource::factory()
        ->for($artist, 'artist')
        ->for($resource, 'resource')
        ->makeOne();

    $artistResource->fill($changes->getAttributes());
    $artistResource->save();

    Event::assertDispatched(ArtistResourceUpdated::class, function (ArtistResourceUpdated $event) {
        $message = $event->getDiscordMessage();

        return ! empty(Arr::get($message->embed, 'fields'));
    });
});
