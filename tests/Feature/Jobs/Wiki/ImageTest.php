<?php

declare(strict_types=1);

use App\Constants\FeatureConstants;
use App\Events\Wiki\Image\ImageCreated;
use App\Events\Wiki\Image\ImageDeleted;
use App\Events\Wiki\Image\ImageRestored;
use App\Events\Wiki\Image\ImageUpdated;
use App\Jobs\SendDiscordNotificationJob;
use App\Models\Wiki\Image;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\Event;
use Laravel\Pennant\Feature;

test('image created sends discord notification', function () {
    Feature::activate(FeatureConstants::ALLOW_DISCORD_NOTIFICATIONS);
    Bus::fake(SendDiscordNotificationJob::class);
    Event::fakeExcept(ImageCreated::class);

    Image::factory()->createOne();

    Bus::assertDispatched(SendDiscordNotificationJob::class);
});

test('image deleted sends discord notification', function () {
    $image = Image::factory()->createOne();

    Feature::activate(FeatureConstants::ALLOW_DISCORD_NOTIFICATIONS);
    Bus::fake(SendDiscordNotificationJob::class);
    Event::fakeExcept(ImageDeleted::class);

    $image->delete();

    Bus::assertDispatched(SendDiscordNotificationJob::class);
});

test('image restored sends discord notification', function () {
    $image = Image::factory()->createOne();

    Feature::activate(FeatureConstants::ALLOW_DISCORD_NOTIFICATIONS);
    Bus::fake(SendDiscordNotificationJob::class);
    Event::fakeExcept(ImageRestored::class);

    $image->restore();

    Bus::assertDispatched(SendDiscordNotificationJob::class);
});

test('image updated sends discord notification', function () {
    $image = Image::factory()->createOne();

    Feature::activate(FeatureConstants::ALLOW_DISCORD_NOTIFICATIONS);
    Bus::fake(SendDiscordNotificationJob::class);
    Event::fakeExcept(ImageUpdated::class);

    $changes = Image::factory()->makeOne();

    $image->fill($changes->getAttributes());
    $image->save();

    Bus::assertDispatched(SendDiscordNotificationJob::class);
});
