<?php

declare(strict_types=1);

use App\Constants\FeatureConstants;
use App\Events\Pivot\Wiki\ArtistResource\ArtistResourceCreated;
use App\Events\Pivot\Wiki\ArtistResource\ArtistResourceDeleted;
use App\Events\Pivot\Wiki\ArtistResource\ArtistResourceUpdated;
use App\Jobs\SendDiscordNotificationJob;
use App\Models\Wiki\Artist;
use App\Models\Wiki\ExternalResource;
use App\Pivots\Wiki\ArtistResource;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\Event;
use Laravel\Pennant\Feature;

test('artist resource created sends discord notification', function () {
    $artist = Artist::factory()->createOne();
    $resource = ExternalResource::factory()->createOne();

    Feature::activate(FeatureConstants::ALLOW_DISCORD_NOTIFICATIONS);
    Bus::fake(SendDiscordNotificationJob::class);
    Event::fakeExcept(ArtistResourceCreated::class);

    $artist->resources()->attach($resource);

    Bus::assertDispatched(SendDiscordNotificationJob::class);
});

test('artist resource deleted sends discord notification', function () {
    $artist = Artist::factory()->createOne();
    $resource = ExternalResource::factory()->createOne();

    $artist->resources()->attach($resource);

    Feature::activate(FeatureConstants::ALLOW_DISCORD_NOTIFICATIONS);
    Bus::fake(SendDiscordNotificationJob::class);
    Event::fakeExcept(ArtistResourceDeleted::class);

    $artist->resources()->detach($resource);

    Bus::assertDispatched(SendDiscordNotificationJob::class);
});

test('artist resource updated sends discord notification', function () {
    $artist = Artist::factory()->createOne();
    $resource = ExternalResource::factory()->createOne();

    $artistResource = ArtistResource::factory()
        ->for($artist, 'artist')
        ->for($resource, 'resource')
        ->createOne();

    $changes = ArtistResource::factory()
        ->for($artist, 'artist')
        ->for($resource, 'resource')
        ->makeOne();

    Feature::activate(FeatureConstants::ALLOW_DISCORD_NOTIFICATIONS);
    Bus::fake(SendDiscordNotificationJob::class);
    Event::fakeExcept(ArtistResourceUpdated::class);

    $artistResource->fill($changes->getAttributes());
    $artistResource->save();

    Bus::assertDispatched(SendDiscordNotificationJob::class);
});
