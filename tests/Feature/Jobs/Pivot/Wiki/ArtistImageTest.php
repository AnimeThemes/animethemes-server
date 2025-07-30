<?php

declare(strict_types=1);

use App\Constants\FeatureConstants;
use App\Events\Pivot\Wiki\ArtistImage\ArtistImageCreated;
use App\Events\Pivot\Wiki\ArtistImage\ArtistImageDeleted;
use App\Jobs\SendDiscordNotificationJob;
use App\Models\Wiki\Artist;
use App\Models\Wiki\Image;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\Event;
use Laravel\Pennant\Feature;

test('artist image created sends discord notification', function () {
    $artist = Artist::factory()->createOne();
    $image = Image::factory()->createOne();

    Feature::activate(FeatureConstants::ALLOW_DISCORD_NOTIFICATIONS);
    Bus::fake(SendDiscordNotificationJob::class);
    Event::fakeExcept(ArtistImageCreated::class);

    $artist->images()->attach($image);

    Bus::assertDispatched(SendDiscordNotificationJob::class);
});

test('artist image deleted sends discord notification', function () {
    $artist = Artist::factory()->createOne();
    $image = Image::factory()->createOne();

    $artist->images()->attach($image);

    Feature::activate(FeatureConstants::ALLOW_DISCORD_NOTIFICATIONS);
    Bus::fake(SendDiscordNotificationJob::class);
    Event::fakeExcept(ArtistImageDeleted::class);

    $artist->images()->detach($image);

    Bus::assertDispatched(SendDiscordNotificationJob::class);
});
