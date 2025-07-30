<?php

declare(strict_types=1);

use App\Events\Wiki\Studio\StudioCreated;
use App\Events\Wiki\Studio\StudioDeleted;
use App\Events\Wiki\Studio\StudioRestored;
use App\Events\Wiki\Studio\StudioUpdated;
use App\Models\Wiki\Studio;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Event;

test('studio created event dispatched', function () {
    Studio::factory()->createOne();

    Event::assertDispatched(StudioCreated::class);
});

test('studio deleted event dispatched', function () {
    $studio = Studio::factory()->createOne();

    $studio->delete();

    Event::assertDispatched(StudioDeleted::class);
});

test('studio restored event dispatched', function () {
    $studio = Studio::factory()->createOne();

    $studio->restore();

    Event::assertDispatched(StudioRestored::class);
});

test('studio restores quietly', function () {
    $studio = Studio::factory()->createOne();

    $studio->restore();

    Event::assertNotDispatched(StudioUpdated::class);
});

test('studio updated event dispatched', function () {
    $studio = Studio::factory()->createOne();
    $changes = Studio::factory()->makeOne();

    $studio->fill($changes->getAttributes());
    $studio->save();

    Event::assertDispatched(StudioUpdated::class);
});

test('studio updated event embed fields', function () {
    $studio = Studio::factory()->createOne();
    $changes = Studio::factory()->makeOne();

    $studio->fill($changes->getAttributes());
    $studio->save();

    Event::assertDispatched(StudioUpdated::class, function (StudioUpdated $event) {
        $message = $event->getDiscordMessage();

        return ! empty(Arr::get($message->embed, 'fields'));
    });
});
