<?php

declare(strict_types=1);

use App\Constants\FeatureConstants;
use App\Events\Pivot\Wiki\AnimeResource\AnimeResourceCreated;
use App\Events\Pivot\Wiki\AnimeResource\AnimeResourceDeleted;
use App\Events\Pivot\Wiki\AnimeResource\AnimeResourceUpdated;
use App\Jobs\SendDiscordNotificationJob;
use App\Models\Wiki\Anime;
use App\Models\Wiki\ExternalResource;
use App\Pivots\Wiki\AnimeResource;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\Event;
use Laravel\Pennant\Feature;

test('anime resource created sends discord notification', function () {
    $anime = Anime::factory()->createOne();
    $resource = ExternalResource::factory()->createOne();

    Feature::activate(FeatureConstants::ALLOW_DISCORD_NOTIFICATIONS);
    Bus::fake(SendDiscordNotificationJob::class);
    Event::fakeExcept(AnimeResourceCreated::class);

    $anime->resources()->attach($resource);

    Bus::assertDispatched(SendDiscordNotificationJob::class);
});

test('anime resource deleted sends discord notification', function () {
    $anime = Anime::factory()->createOne();
    $resource = ExternalResource::factory()->createOne();

    $anime->resources()->attach($resource);

    Feature::activate(FeatureConstants::ALLOW_DISCORD_NOTIFICATIONS);
    Bus::fake(SendDiscordNotificationJob::class);
    Event::fakeExcept(AnimeResourceDeleted::class);

    $anime->resources()->detach($resource);

    Bus::assertDispatched(SendDiscordNotificationJob::class);
});

test('anime resource updated sends discord notification', function () {
    $anime = Anime::factory()->createOne();
    $resource = ExternalResource::factory()->createOne();

    $animeResource = AnimeResource::factory()
        ->for($anime, 'anime')
        ->for($resource, 'resource')
        ->createOne();

    $changes = AnimeResource::factory()
        ->for($anime, 'anime')
        ->for($resource, 'resource')
        ->makeOne();

    Feature::activate(FeatureConstants::ALLOW_DISCORD_NOTIFICATIONS);
    Bus::fake(SendDiscordNotificationJob::class);
    Event::fakeExcept(AnimeResourceUpdated::class);

    $animeResource->fill($changes->getAttributes());
    $animeResource->save();

    Bus::assertDispatched(SendDiscordNotificationJob::class);
});
