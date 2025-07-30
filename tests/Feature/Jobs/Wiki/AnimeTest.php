<?php

declare(strict_types=1);

use App\Constants\FeatureConstants;
use App\Events\Wiki\Anime\AnimeCreated;
use App\Events\Wiki\Anime\AnimeDeleted;
use App\Events\Wiki\Anime\AnimeRestored;
use App\Events\Wiki\Anime\AnimeUpdated;
use App\Jobs\SendDiscordNotificationJob;
use App\Models\Wiki\Anime;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\Event;
use Laravel\Pennant\Feature;

test('anime created sends discord notification', function () {
    Feature::activate(FeatureConstants::ALLOW_DISCORD_NOTIFICATIONS);
    Bus::fake(SendDiscordNotificationJob::class);
    Event::fakeExcept(AnimeCreated::class);

    Anime::factory()->createOne();

    Bus::assertDispatched(SendDiscordNotificationJob::class);
});

test('anime deleted sends discord notification', function () {
    $anime = Anime::factory()->createOne();

    Feature::activate(FeatureConstants::ALLOW_DISCORD_NOTIFICATIONS);
    Bus::fake(SendDiscordNotificationJob::class);
    Event::fakeExcept(AnimeDeleted::class);

    $anime->delete();

    Bus::assertDispatched(SendDiscordNotificationJob::class);
});

test('anime restored sends discord notification', function () {
    $anime = Anime::factory()->createOne();

    Feature::activate(FeatureConstants::ALLOW_DISCORD_NOTIFICATIONS);
    Bus::fake(SendDiscordNotificationJob::class);
    Event::fakeExcept(AnimeRestored::class);

    $anime->restore();

    Bus::assertDispatched(SendDiscordNotificationJob::class);
});

test('anime updated sends discord notification', function () {
    $anime = Anime::factory()->createOne();

    Feature::activate(FeatureConstants::ALLOW_DISCORD_NOTIFICATIONS);
    Bus::fake(SendDiscordNotificationJob::class);
    Event::fakeExcept(AnimeUpdated::class);

    $changes = Anime::factory()->makeOne();

    $anime->fill($changes->getAttributes());
    $anime->save();

    Bus::assertDispatched(SendDiscordNotificationJob::class);
});
