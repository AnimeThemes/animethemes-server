<?php

declare(strict_types=1);

use App\Constants\FeatureConstants;
use App\Events\Pivot\Wiki\StudioResource\StudioResourceCreated;
use App\Events\Pivot\Wiki\StudioResource\StudioResourceDeleted;
use App\Events\Pivot\Wiki\StudioResource\StudioResourceUpdated;
use App\Jobs\SendDiscordNotificationJob;
use App\Models\Wiki\ExternalResource;
use App\Models\Wiki\Studio;
use App\Pivots\Wiki\StudioResource;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\Event;
use Laravel\Pennant\Feature;

test('studio resource created sends discord notification', function () {
    $studio = Studio::factory()->createOne();
    $resource = ExternalResource::factory()->createOne();

    Feature::activate(FeatureConstants::ALLOW_DISCORD_NOTIFICATIONS);
    Bus::fake(SendDiscordNotificationJob::class);
    Event::fakeExcept(StudioResourceCreated::class);

    $studio->resources()->attach($resource);

    Bus::assertDispatched(SendDiscordNotificationJob::class);
});

test('studio resource deleted sends discord notification', function () {
    $studio = Studio::factory()->createOne();
    $resource = ExternalResource::factory()->createOne();

    $studio->resources()->attach($resource);

    Feature::activate(FeatureConstants::ALLOW_DISCORD_NOTIFICATIONS);
    Bus::fake(SendDiscordNotificationJob::class);
    Event::fakeExcept(StudioResourceDeleted::class);

    $studio->resources()->detach($resource);

    Bus::assertDispatched(SendDiscordNotificationJob::class);
});

test('studio resource updated sends discord notification', function () {
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

    Feature::activate(FeatureConstants::ALLOW_DISCORD_NOTIFICATIONS);
    Bus::fake(SendDiscordNotificationJob::class);
    Event::fakeExcept(StudioResourceUpdated::class);

    $studioResource->fill($changes->getAttributes());
    $studioResource->save();

    Bus::assertDispatched(SendDiscordNotificationJob::class);
});
