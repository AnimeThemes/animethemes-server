<?php

declare(strict_types=1);

use App\Constants\FeatureConstants;
use App\Events\Pivot\Wiki\AnimeImage\AnimeImageCreated;
use App\Events\Pivot\Wiki\AnimeImage\AnimeImageDeleted;
use App\Jobs\SendDiscordNotificationJob;
use App\Models\Wiki\Anime;
use App\Models\Wiki\Image;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\Event;
use Laravel\Pennant\Feature;

test('anime image created sends discord notification', function () {
    $anime = Anime::factory()->createOne();
    $image = Image::factory()->createOne();

    Feature::activate(FeatureConstants::ALLOW_DISCORD_NOTIFICATIONS);
    Bus::fake(SendDiscordNotificationJob::class);
    Event::fakeExcept(AnimeImageCreated::class);

    $anime->images()->attach($image);

    Bus::assertDispatched(SendDiscordNotificationJob::class);
});

test('anime image deleted sends discord notification', function () {
    $anime = Anime::factory()->createOne();
    $image = Image::factory()->createOne();

    $anime->images()->attach($image);

    Feature::activate(FeatureConstants::ALLOW_DISCORD_NOTIFICATIONS);
    Bus::fake(SendDiscordNotificationJob::class);
    Event::fakeExcept(AnimeImageDeleted::class);

    $anime->images()->detach($image);

    Bus::assertDispatched(SendDiscordNotificationJob::class);
});
