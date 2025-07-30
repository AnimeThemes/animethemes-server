<?php

declare(strict_types=1);

use App\Constants\FeatureConstants;
use App\Events\Pivot\Wiki\AnimeSeries\AnimeSeriesCreated;
use App\Events\Pivot\Wiki\AnimeSeries\AnimeSeriesDeleted;
use App\Jobs\SendDiscordNotificationJob;
use App\Models\Wiki\Anime;
use App\Models\Wiki\Series;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\Event;
use Laravel\Pennant\Feature;

test('anime series created sends discord notification', function () {
    $anime = Anime::factory()->createOne();
    $series = Series::factory()->createOne();

    Feature::activate(FeatureConstants::ALLOW_DISCORD_NOTIFICATIONS);
    Bus::fake(SendDiscordNotificationJob::class);
    Event::fakeExcept(AnimeSeriesCreated::class);

    $anime->series()->attach($series);

    Bus::assertDispatched(SendDiscordNotificationJob::class);
});

test('anime series deleted sends discord notification', function () {
    $anime = Anime::factory()->createOne();
    $series = Series::factory()->createOne();

    $anime->series()->attach($series);

    Feature::activate(FeatureConstants::ALLOW_DISCORD_NOTIFICATIONS);
    Bus::fake(SendDiscordNotificationJob::class);
    Event::fakeExcept(AnimeSeriesDeleted::class);

    $anime->series()->detach($series);

    Bus::assertDispatched(SendDiscordNotificationJob::class);
});
