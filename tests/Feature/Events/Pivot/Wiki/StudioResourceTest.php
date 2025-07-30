<?php

declare(strict_types=1);

use App\Events\Pivot\Wiki\StudioResource\StudioResourceCreated;
use App\Events\Pivot\Wiki\StudioResource\StudioResourceDeleted;
use App\Events\Pivot\Wiki\StudioResource\StudioResourceUpdated;
use App\Models\Wiki\ExternalResource;
use App\Models\Wiki\Studio;
use App\Pivots\Wiki\StudioResource;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Event;

test('studio resource created event dispatched', function () {
    $studio = Studio::factory()->createOne();
    $resource = ExternalResource::factory()->createOne();

    $studio->resources()->attach($resource);

    Event::assertDispatched(StudioResourceCreated::class);
});

test('studio resource deleted event dispatched', function () {
    $studio = Studio::factory()->createOne();
    $resource = ExternalResource::factory()->createOne();

    $studio->resources()->attach($resource);
    $studio->resources()->detach($resource);

    Event::assertDispatched(StudioResourceDeleted::class);
});

test('studio resource updated event dispatched', function () {
    $studio = Studio::factory()->createOne();
    $resource = ExternalResource::factory()->createOne();

    $studioResource = StudioResource::factory()
        ->for($studio, 'studio')
        ->for($resource, 'resource')
        ->createOne();

    $changes = StudioResource::factory()
        ->for($studio, 'studio')
        ->for($resource, 'resource')
        ->makeOne();

    $studioResource->fill($changes->getAttributes());
    $studioResource->save();

    Event::assertDispatched(StudioResourceUpdated::class);
});

test('studio resource updated event embed fields', function () {
    $studio = Studio::factory()->createOne();
    $resource = ExternalResource::factory()->createOne();

    $studioResource = StudioResource::factory()
        ->for($studio, 'studio')
        ->for($resource, 'resource')
        ->createOne();

    $changes = StudioResource::factory()
        ->for($studio, 'studio')
        ->for($resource, 'resource')
        ->makeOne();

    $studioResource->fill($changes->getAttributes());
    $studioResource->save();

    Event::assertDispatched(StudioResourceUpdated::class, function (StudioResourceUpdated $event) {
        $message = $event->getDiscordMessage();

        return ! empty(Arr::get($message->embed, 'fields'));
    });
});
