<?php

declare(strict_types=1);

use App\Events\Wiki\Song\Performance\PerformanceCreated;
use App\Events\Wiki\Song\Performance\PerformanceDeleted;
use App\Events\Wiki\Song\Performance\PerformanceRestored;
use App\Events\Wiki\Song\Performance\PerformanceUpdated;
use App\Models\Wiki\Song\Performance;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Event;

test('performance created event dispatched', function () {
    Performance::factory()->createOne();

    Event::assertDispatched(PerformanceCreated::class);
});

test('performance deleted event dispatched', function () {
    $performance = Performance::factory()->createOne();

    $performance->delete();

    Event::assertDispatched(PerformanceDeleted::class);
});

test('performance restored event dispatched', function () {
    $performance = Performance::factory()->createOne();

    $performance->restore();

    Event::assertDispatched(PerformanceRestored::class);
});

test('performance restores quietly', function () {
    $performance = Performance::factory()->createOne();

    $performance->restore();

    Event::assertNotDispatched(PerformanceUpdated::class);
});

test('performance updated event dispatched', function () {
    $performance = Performance::factory()->createOne();
    $changes = Performance::factory()->makeOne();

    $performance->fill($changes->getAttributes());
    $performance->save();

    Event::assertDispatched(PerformanceUpdated::class);
});

test('performance updated event embed fields', function () {
    $performance = Performance::factory()->createOne();
    $changes = Performance::factory()->makeOne();

    $performance->fill($changes->getAttributes());
    $performance->save();

    Event::assertDispatched(PerformanceUpdated::class, function (PerformanceUpdated $event) {
        $message = $event->getDiscordMessage();

        return ! empty(Arr::get($message->embed, 'fields'));
    });
});
