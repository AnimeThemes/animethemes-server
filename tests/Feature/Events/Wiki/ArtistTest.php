<?php

declare(strict_types=1);

use App\Events\Wiki\Artist\ArtistCreated;
use App\Events\Wiki\Artist\ArtistDeleted;
use App\Events\Wiki\Artist\ArtistRestored;
use App\Events\Wiki\Artist\ArtistUpdated;
use App\Models\Wiki\Artist;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Event;

test('artist created event dispatched', function (): void {
    Artist::factory()->createOne();

    Event::assertDispatched(ArtistCreated::class);
});

test('artist deleted event dispatched', function (): void {
    $artist = Artist::factory()->createOne();

    $artist->delete();

    Event::assertDispatched(ArtistDeleted::class);
});

test('artist restored event dispatched', function (): void {
    $artist = Artist::factory()->createOne();

    $artist->restore();

    Event::assertDispatched(ArtistRestored::class);
});

test('artist restores quietly', function (): void {
    $artist = Artist::factory()->createOne();

    $artist->restore();

    Event::assertNotDispatched(ArtistUpdated::class);
});

test('artist updated event dispatched', function (): void {
    $artist = Artist::factory()->createOne();
    $changes = Artist::factory()->makeOne();

    $artist->fill($changes->getAttributes());
    $artist->save();

    Event::assertDispatched(ArtistUpdated::class);
});

test('artist updated event embed fields', function (): void {
    $artist = Artist::factory()->createOne();
    $changes = Artist::factory()->makeOne();

    $artist->fill($changes->getAttributes());
    $artist->save();

    Event::assertDispatched(ArtistUpdated::class, function (ArtistUpdated $event): bool {
        $message = $event->getDiscordMessage();

        return filled(Arr::get($message->embed, 'fields'));
    });
});
