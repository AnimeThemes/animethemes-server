<?php

declare(strict_types=1);

use App\Events\Wiki\Series\SeriesCreated;
use App\Events\Wiki\Series\SeriesDeleted;
use App\Events\Wiki\Series\SeriesRestored;
use App\Events\Wiki\Series\SeriesUpdated;
use App\Models\Wiki\Series;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Event;

test('series created event dispatched', function () {
    Series::factory()->createOne();

    Event::assertDispatched(SeriesCreated::class);
});

test('series deleted event dispatched', function () {
    $series = Series::factory()->createOne();

    $series->delete();

    Event::assertDispatched(SeriesDeleted::class);
});

test('series restored event dispatched', function () {
    $series = Series::factory()->createOne();

    $series->restore();

    Event::assertDispatched(SeriesRestored::class);
});

test('series restores quietly', function () {
    $series = Series::factory()->createOne();

    $series->restore();

    Event::assertNotDispatched(SeriesUpdated::class);
});

test('series updated event dispatched', function () {
    $series = Series::factory()->createOne();
    $changes = Series::factory()->makeOne();

    $series->fill($changes->getAttributes());
    $series->save();

    Event::assertDispatched(SeriesUpdated::class);
});

test('series updated event embed fields', function () {
    $series = Series::factory()->createOne();
    $changes = Series::factory()->makeOne();

    $series->fill($changes->getAttributes());
    $series->save();

    Event::assertDispatched(SeriesUpdated::class, function (SeriesUpdated $event) {
        $message = $event->getDiscordMessage();

        return ! empty(Arr::get($message->embed, 'fields'));
    });
});
