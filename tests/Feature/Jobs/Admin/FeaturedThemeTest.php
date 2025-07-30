<?php

declare(strict_types=1);

use App\Constants\FeatureConstants;
use App\Events\Admin\FeaturedTheme\FeaturedThemeCreated;
use App\Events\Admin\FeaturedTheme\FeaturedThemeDeleted;
use App\Events\Admin\FeaturedTheme\FeaturedThemeUpdated;
use App\Jobs\SendDiscordNotificationJob;
use App\Models\Admin\FeaturedTheme;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\Event;
use Laravel\Pennant\Feature;

test('featured theme created sends discord notification', function () {
    Feature::activate(FeatureConstants::ALLOW_DISCORD_NOTIFICATIONS);
    Bus::fake(SendDiscordNotificationJob::class);
    Event::fakeExcept(FeaturedThemeCreated::class);

    FeaturedTheme::factory()->createOne();

    Bus::assertDispatched(SendDiscordNotificationJob::class);
});

test('featured theme deleted sends discord notification', function () {
    $featuredTheme = FeaturedTheme::factory()->createOne();

    Feature::activate(FeatureConstants::ALLOW_DISCORD_NOTIFICATIONS);
    Bus::fake(SendDiscordNotificationJob::class);
    Event::fakeExcept(FeaturedThemeDeleted::class);

    $featuredTheme->delete();

    Bus::assertDispatched(SendDiscordNotificationJob::class);
});

test('featured theme updated sends discord notification', function () {
    $featuredTheme = FeaturedTheme::factory()->createOne();

    Feature::activate(FeatureConstants::ALLOW_DISCORD_NOTIFICATIONS);
    Bus::fake(SendDiscordNotificationJob::class);
    Event::fakeExcept(FeaturedThemeUpdated::class);

    $changes = FeaturedTheme::factory()->makeOne();

    $featuredTheme->fill($changes->getAttributes());
    $featuredTheme->save();

    Bus::assertDispatched(SendDiscordNotificationJob::class);
});
