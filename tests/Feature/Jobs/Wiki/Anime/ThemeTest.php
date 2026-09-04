<?php

declare(strict_types=1);

use App\Constants\FeatureConstants;
use App\Events\Wiki\Theme\ThemeCreated;
use App\Events\Wiki\Theme\ThemeDeleted;
use App\Events\Wiki\Theme\ThemeRestored;
use App\Events\Wiki\Theme\ThemeUpdated;
use App\Jobs\SendDiscordNotificationJob;
use App\Models\Wiki\Anime;
use App\Models\Wiki\Theme;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\Event;
use Laravel\Pennant\Feature;

test('theme created sends discord notification', function (): void {
    $anime = Anime::factory()->createOne();

    Feature::activate(FeatureConstants::ALLOW_DISCORD_NOTIFICATIONS);
    Bus::fake(SendDiscordNotificationJob::class);
    Event::fakeExcept(ThemeCreated::class);

    Theme::factory()->for($anime)->createOne();

    Bus::assertDispatched(SendDiscordNotificationJob::class);
});

test('theme deleted sends discord notification', function (): void {
    $theme = Theme::factory()
        ->for(Anime::factory())
        ->createOne();

    Feature::activate(FeatureConstants::ALLOW_DISCORD_NOTIFICATIONS);
    Bus::fake(SendDiscordNotificationJob::class);
    Event::fakeExcept(ThemeDeleted::class);

    $theme->delete();

    Bus::assertDispatched(SendDiscordNotificationJob::class);
});

test('theme restored sends discord notification', function (): void {
    $theme = Theme::factory()
        ->for(Anime::factory())
        ->createOne();

    Feature::activate(FeatureConstants::ALLOW_DISCORD_NOTIFICATIONS);
    Bus::fake(SendDiscordNotificationJob::class);
    Event::fakeExcept(ThemeRestored::class);

    $theme->restore();

    Bus::assertDispatched(SendDiscordNotificationJob::class);
});

test('theme updated sends discord notification', function (): void {
    $theme = Theme::factory()
        ->for(Anime::factory())
        ->createOne();

    $changes = Theme::factory()
        ->for(Anime::factory())
        ->makeOne();

    Feature::activate(FeatureConstants::ALLOW_DISCORD_NOTIFICATIONS);
    Bus::fake(SendDiscordNotificationJob::class);
    Event::fakeExcept(ThemeUpdated::class);

    $theme->fill($changes->getAttributes());
    $theme->save();

    Bus::assertDispatched(SendDiscordNotificationJob::class);
});
