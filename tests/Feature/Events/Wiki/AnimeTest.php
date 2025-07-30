<?php

declare(strict_types=1);

use App\Events\Wiki\Anime\AnimeCreated;
use App\Events\Wiki\Anime\AnimeDeleted;
use App\Events\Wiki\Anime\AnimeRestored;
use App\Events\Wiki\Anime\AnimeUpdated;
use App\Models\Wiki\Anime;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Event;

test('anime created event dispatched', function () {
    Anime::factory()->createOne();

    Event::assertDispatched(AnimeCreated::class);
});

test('anime deleted event dispatched', function () {
    $anime = Anime::factory()->createOne();

    $anime->delete();

    Event::assertDispatched(AnimeDeleted::class);
});

test('anime restored event dispatched', function () {
    $anime = Anime::factory()->createOne();

    $anime->restore();

    Event::assertDispatched(AnimeRestored::class);
});

test('anime restores quietly', function () {
    $anime = Anime::factory()->createOne();

    $anime->restore();

    Event::assertNotDispatched(AnimeUpdated::class);
});

test('anime updated event dispatched', function () {
    $anime = Anime::factory()->createOne();
    $changes = Anime::factory()->makeOne();

    $anime->fill($changes->getAttributes());
    $anime->save();

    Event::assertDispatched(AnimeUpdated::class);
});

test('anime updated event embed fields', function () {
    $anime = Anime::factory()->createOne();
    $changes = Anime::factory()->makeOne();

    $anime->fill($changes->getAttributes());
    $anime->save();

    Event::assertDispatched(AnimeUpdated::class, function (AnimeUpdated $event) {
        $message = $event->getDiscordMessage();

        return ! empty(Arr::get($message->embed, 'fields'));
    });
});
