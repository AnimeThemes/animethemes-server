<?php

declare(strict_types=1);

use App\Events\Pivot\Wiki\AnimeResource\AnimeResourceCreated;
use App\Events\Pivot\Wiki\AnimeResource\AnimeResourceDeleted;
use App\Events\Pivot\Wiki\AnimeResource\AnimeResourceUpdated;
use App\Models\Wiki\Anime;
use App\Models\Wiki\ExternalResource;
use App\Pivots\Wiki\AnimeResource;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Event;

test('anime resource created event dispatched', function () {
    $anime = Anime::factory()->createOne();
    $resource = ExternalResource::factory()->createOne();

    $anime->resources()->attach($resource);

    Event::assertDispatched(AnimeResourceCreated::class);
});

test('anime resource deleted event dispatched', function () {
    $anime = Anime::factory()->createOne();
    $resource = ExternalResource::factory()->createOne();

    $anime->resources()->attach($resource);
    $anime->resources()->detach($resource);

    Event::assertDispatched(AnimeResourceDeleted::class);
});

test('anime resource updated event dispatched', function () {
    $anime = Anime::factory()->createOne();
    $resource = ExternalResource::factory()->createOne();

    $animeResource = AnimeResource::factory()
        ->for($anime, 'anime')
        ->for($resource, 'resource')
        ->createOne();

    $changes = AnimeResource::factory()
        ->for($anime, 'anime')
        ->for($resource, 'resource')
        ->makeOne();

    $animeResource->fill($changes->getAttributes());
    $animeResource->save();

    Event::assertDispatched(AnimeResourceUpdated::class);
});

test('anime resource updated event embed fields', function () {
    $anime = Anime::factory()->createOne();
    $resource = ExternalResource::factory()->createOne();

    $animeResource = AnimeResource::factory()
        ->for($anime, 'anime')
        ->for($resource, 'resource')
        ->createOne();

    $changes = AnimeResource::factory()
        ->for($anime, 'anime')
        ->for($resource, 'resource')
        ->makeOne();

    $animeResource->fill($changes->getAttributes());
    $animeResource->save();

    Event::assertDispatched(AnimeResourceUpdated::class, function (AnimeResourceUpdated $event) {
        $message = $event->getDiscordMessage();

        return ! empty(Arr::get($message->embed, 'fields'));
    });
});
