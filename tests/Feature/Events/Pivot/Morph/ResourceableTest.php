<?php

declare(strict_types=1);

use App\Events\Pivot\Morph\Resourceable\ResourceableCreated;
use App\Events\Pivot\Morph\Resourceable\ResourceableDeleted;
use App\Events\Pivot\Morph\Resourceable\ResourceableUpdated;
use App\Models\Wiki\ExternalResource;
use App\Pivots\Morph\Resourceable;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Event;

test('resourceable created event dispatched', function () {
    $modelClass = Arr::random(Resourceable::$resourceables);

    $model = $modelClass::factory()->createOne();
    $resource = ExternalResource::factory()->createOne();

    $model->resources()->attach($resource);

    Event::assertDispatched(ResourceableCreated::class);
});

test('resourceable deleted event dispatched', function () {
    $modelClass = Arr::random(Resourceable::$resourceables);

    $model = $modelClass::factory()->createOne();
    $resource = ExternalResource::factory()->createOne();

    $model->resources()->attach($resource);
    $model->resources()->detach($resource);

    Event::assertDispatched(ResourceableDeleted::class);
});

test('resourceable updated event dispatched', function () {
    $modelClass = Arr::random(Resourceable::$resourceables);

    $model = $modelClass::factory()->createOne();
    $resource = ExternalResource::factory()->createOne();

    $resourceable = Resourceable::factory()
        ->for($model, Resourceable::RELATION_RESOURCEABLE)
        ->for($resource, Resourceable::RELATION_RESOURCE)
        ->createOne();

    $changes = Resourceable::factory()
        ->for($model, Resourceable::RELATION_RESOURCEABLE)
        ->for($resource, Resourceable::RELATION_RESOURCE)
        ->makeOne();

    $resourceable->fill($changes->getAttributes());
    $resourceable->save();

    Event::assertDispatched(ResourceableUpdated::class);
});

test('resourceable updated event embed fields', function () {
    $modelClass = Arr::random(Resourceable::$resourceables);

    $model = $modelClass::factory()->createOne();
    $resource = ExternalResource::factory()->createOne();

    $resourceable = Resourceable::factory()
        ->for($model, Resourceable::RELATION_RESOURCEABLE)
        ->for($resource, Resourceable::RELATION_RESOURCE)
        ->createOne();

    $changes = Resourceable::factory()
        ->for($model, Resourceable::RELATION_RESOURCEABLE)
        ->for($resource, Resourceable::RELATION_RESOURCE)
        ->makeOne();

    $resourceable->fill($changes->getAttributes());
    $resourceable->save();

    Event::assertDispatched(ResourceableUpdated::class, function (ResourceableUpdated $event) {
        $message = $event->getDiscordMessage();

        return ! empty(Arr::get($message->embed, 'fields'));
    });
});
