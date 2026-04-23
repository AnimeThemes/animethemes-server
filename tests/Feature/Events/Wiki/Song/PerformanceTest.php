<?php

declare(strict_types=1);

use App\Events\Wiki\Song\Performance\PerformanceCreated;
use App\Events\Wiki\Song\Performance\PerformanceDeleted;
use App\Events\Wiki\Song\Performance\PerformanceRestored;
use App\Events\Wiki\Song\Performance\PerformanceUpdated;
use App\Models\Wiki\Song\Performance;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Event;

test('performance created event dispatched', function (): void {
    Performance::factory()->createOne();

    Event::assertDispatched(PerformanceCreated::class);
});

test('performance deleted event dispatched', function (): void {
    $performance = Performance::factory()->createOne();

    $performance->delete();

    Event::assertDispatched(PerformanceDeleted::class);
});

test('performance restored event dispatched', function (): void {
    $performance = Performance::factory()->createOne();

    $performance->restore();

    Event::assertDispatched(PerformanceRestored::class);
});

test('performance restores quietly', function (): void {
    $performance = Performance::factory()->createOne();

    $performance->restore();

    Event::assertNotDispatched(PerformanceUpdated::class);
});

test('performance updated event dispatched', function (): void {
    $performance = Performance::factory()->createOne();
    $changes = Performance::factory()->makeOne();

    $performance->fill($changes->getAttributes());
    $performance->save();

    Event::assertDispatched(PerformanceUpdated::class);
});

test('performance updated event embed fields', function (): void {
    $performance = Performance::factory()->createOne();
    $changes = Performance::factory()->makeOne();

    $performance->fill($changes->getAttributes());
    $performance->save();

    Event::assertDispatched(PerformanceUpdated::class, function (PerformanceUpdated $event): bool {
        $message = $event->getDiscordMessage();

        return filled(Arr::get($message->embed, 'fields'));
    });
});
