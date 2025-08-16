<?php

declare(strict_types=1);

use App\Constants\FeatureConstants;
use App\Events\Pivot\Morph\Resourceable\ResourceableCreated;
use App\Events\Pivot\Morph\Resourceable\ResourceableDeleted;
use App\Events\Pivot\Morph\Resourceable\ResourceableUpdated;
use App\Jobs\SendDiscordNotificationJob;
use App\Models\Wiki\ExternalResource;
use App\Pivots\Morph\Resourceable;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\Event;
use Laravel\Pennant\Feature;

test('resourceable created sends discord notification', function () {
    $modelClass = Arr::random(Resourceable::$resourceables);

    $model = $modelClass::factory()->createOne();
    $resource = ExternalResource::factory()->createOne();

    Feature::activate(FeatureConstants::ALLOW_DISCORD_NOTIFICATIONS);
    Bus::fake(SendDiscordNotificationJob::class);
    Event::fakeExcept(ResourceableCreated::class);

    $model->resources()->attach($resource);

    Bus::assertDispatched(SendDiscordNotificationJob::class);
});

test('resourceable deleted sends discord notification', function () {
    $modelClass = Arr::random(Resourceable::$resourceables);

    $model = $modelClass::factory()->createOne();
    $resource = ExternalResource::factory()->createOne();

    $model->resources()->attach($resource);

    Feature::activate(FeatureConstants::ALLOW_DISCORD_NOTIFICATIONS);
    Bus::fake(SendDiscordNotificationJob::class);
    Event::fakeExcept(ResourceableDeleted::class);

    $model->resources()->detach($resource);

    Bus::assertDispatched(SendDiscordNotificationJob::class);
});

test('resourceable updated sends discord notification', function () {
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

    Feature::activate(FeatureConstants::ALLOW_DISCORD_NOTIFICATIONS);
    Bus::fake(SendDiscordNotificationJob::class);
    Event::fakeExcept(ResourceableUpdated::class);

    $resourceable->fill($changes->getAttributes());
    $resourceable->save();

    Bus::assertDispatched(SendDiscordNotificationJob::class);
});
