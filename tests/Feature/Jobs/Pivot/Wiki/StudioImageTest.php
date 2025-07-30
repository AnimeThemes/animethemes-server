<?php

declare(strict_types=1);

use App\Constants\FeatureConstants;
use App\Events\Pivot\Wiki\StudioImage\StudioImageCreated;
use App\Events\Pivot\Wiki\StudioImage\StudioImageDeleted;
use App\Jobs\SendDiscordNotificationJob;
use App\Models\Wiki\Image;
use App\Models\Wiki\Studio;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\Event;
use Laravel\Pennant\Feature;

test('studio image created sends discord notification', function () {
    $studio = Studio::factory()->createOne();
    $image = Image::factory()->createOne();

    Feature::activate(FeatureConstants::ALLOW_DISCORD_NOTIFICATIONS);
    Bus::fake(SendDiscordNotificationJob::class);
    Event::fakeExcept(StudioImageCreated::class);

    $studio->images()->attach($image);

    Bus::assertDispatched(SendDiscordNotificationJob::class);
});

test('studio image deleted sends discord notification', function () {
    $studio = Studio::factory()->createOne();
    $image = Image::factory()->createOne();

    $studio->images()->attach($image);

    Feature::activate(FeatureConstants::ALLOW_DISCORD_NOTIFICATIONS);
    Bus::fake(SendDiscordNotificationJob::class);
    Event::fakeExcept(StudioImageDeleted::class);

    $studio->images()->detach($image);

    Bus::assertDispatched(SendDiscordNotificationJob::class);
});
