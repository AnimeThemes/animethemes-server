<?php

declare(strict_types=1);

use App\Constants\FeatureConstants;
use App\Events\Wiki\Artist\ArtistCreated;
use App\Events\Wiki\Artist\ArtistDeleted;
use App\Events\Wiki\Artist\ArtistRestored;
use App\Events\Wiki\Artist\ArtistUpdated;
use App\Jobs\SendDiscordNotificationJob;
use App\Models\Wiki\Artist;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\Event;
use Laravel\Pennant\Feature;

test('artist created sends discord notification', function () {
    Feature::activate(FeatureConstants::ALLOW_DISCORD_NOTIFICATIONS);
    Bus::fake(SendDiscordNotificationJob::class);
    Event::fakeExcept(ArtistCreated::class);

    Artist::factory()->createOne();

    Bus::assertDispatched(SendDiscordNotificationJob::class);
});

test('artist deleted sends discord notification', function () {
    $artist = Artist::factory()->createOne();

    Feature::activate(FeatureConstants::ALLOW_DISCORD_NOTIFICATIONS);
    Bus::fake(SendDiscordNotificationJob::class);
    Event::fakeExcept(ArtistDeleted::class);

    $artist->delete();

    Bus::assertDispatched(SendDiscordNotificationJob::class);
});

test('artist restored sends discord notification', function () {
    $artist = Artist::factory()->createOne();

    Feature::activate(FeatureConstants::ALLOW_DISCORD_NOTIFICATIONS);
    Bus::fake(SendDiscordNotificationJob::class);
    Event::fakeExcept(ArtistRestored::class);

    $artist->restore();

    Bus::assertDispatched(SendDiscordNotificationJob::class);
});

test('artist updated sends discord notification', function () {
    $artist = Artist::factory()->createOne();

    Feature::activate(FeatureConstants::ALLOW_DISCORD_NOTIFICATIONS);
    Bus::fake(SendDiscordNotificationJob::class);
    Event::fakeExcept(ArtistUpdated::class);

    $changes = Artist::factory()->makeOne();

    $artist->fill($changes->getAttributes());
    $artist->save();

    Bus::assertDispatched(SendDiscordNotificationJob::class);
});
