<?php

declare(strict_types=1);

use App\Events\Wiki\Studio\StudioCreated;
use App\Events\Wiki\Studio\StudioDeleted;
use App\Events\Wiki\Studio\StudioRestored;
use App\Events\Wiki\Studio\StudioUpdated;
use App\Models\Wiki\Studio;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Event;

test('studio created event dispatched', function (): void {
    Studio::factory()->createOne();

    Event::assertDispatched(StudioCreated::class);
});

test('studio deleted event dispatched', function (): void {
    $studio = Studio::factory()->createOne();

    $studio->delete();

    Event::assertDispatched(StudioDeleted::class);
});

test('studio restored event dispatched', function (): void {
    $studio = Studio::factory()->createOne();

    $studio->restore();

    Event::assertDispatched(StudioRestored::class);
});

test('studio restores quietly', function (): void {
    $studio = Studio::factory()->createOne();

    $studio->restore();

    Event::assertNotDispatched(StudioUpdated::class);
});

test('studio updated event dispatched', function (): void {
    $studio = Studio::factory()->createOne();
    $changes = Studio::factory()->makeOne();

    $studio->fill($changes->getAttributes());
    $studio->save();

    Event::assertDispatched(StudioUpdated::class);
});

test('studio updated event embed fields', function (): void {
    $studio = Studio::factory()->createOne();
    $changes = Studio::factory()->makeOne();

    $studio->fill($changes->getAttributes());
    $studio->save();

    Event::assertDispatched(StudioUpdated::class, function (StudioUpdated $event): bool {
        $message = $event->getDiscordMessage();

        return filled(Arr::get($message->embed, 'fields'));
    });
});
