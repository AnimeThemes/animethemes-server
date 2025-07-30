<?php

declare(strict_types=1);

use App\Events\Wiki\ExternalResource\ExternalResourceCreated;
use App\Events\Wiki\ExternalResource\ExternalResourceDeleted;
use App\Events\Wiki\ExternalResource\ExternalResourceRestored;
use App\Events\Wiki\ExternalResource\ExternalResourceUpdated;
use App\Models\Wiki\ExternalResource;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Event;

test('external resource created event dispatched', function () {
    ExternalResource::factory()->createOne();

    Event::assertDispatched(ExternalResourceCreated::class);
});

test('external resource deleted event dispatched', function () {
    $resource = ExternalResource::factory()->createOne();

    $resource->delete();

    Event::assertDispatched(ExternalResourceDeleted::class);
});

test('external resource restored event dispatched', function () {
    $resource = ExternalResource::factory()->createOne();

    $resource->restore();

    Event::assertDispatched(ExternalResourceRestored::class);
});

test('external resource restores quietly', function () {
    $resource = ExternalResource::factory()->createOne();

    $resource->restore();

    Event::assertNotDispatched(ExternalResourceUpdated::class);
});

test('external resource updated event dispatched', function () {
    $resource = ExternalResource::factory()->createOne();
    $changes = ExternalResource::factory()->makeOne();

    $resource->fill($changes->getAttributes());
    $resource->save();

    Event::assertDispatched(ExternalResourceUpdated::class);
});

test('external resource updated event embed fields', function () {
    $resource = ExternalResource::factory()->createOne();
    $changes = ExternalResource::factory()->makeOne();

    $resource->fill($changes->getAttributes());
    $resource->save();

    Event::assertDispatched(ExternalResourceUpdated::class, function (ExternalResourceUpdated $event) {
        $message = $event->getDiscordMessage();

        return ! empty(Arr::get($message->embed, 'fields'));
    });
});
