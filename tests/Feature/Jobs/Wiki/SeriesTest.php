<?php

declare(strict_types=1);

use App\Constants\FeatureConstants;
use App\Events\Wiki\Series\SeriesCreated;
use App\Events\Wiki\Series\SeriesDeleted;
use App\Events\Wiki\Series\SeriesRestored;
use App\Events\Wiki\Series\SeriesUpdated;
use App\Jobs\SendDiscordNotificationJob;
use App\Models\Wiki\Series;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\Event;
use Laravel\Pennant\Feature;

test('series created sends discord notification', function () {
    Feature::activate(FeatureConstants::ALLOW_DISCORD_NOTIFICATIONS);
    Bus::fake(SendDiscordNotificationJob::class);
    Event::fakeExcept(SeriesCreated::class);

    Series::factory()->createOne();

    Bus::assertDispatched(SendDiscordNotificationJob::class);
});

test('series deleted sends discord notification', function () {
    $series = Series::factory()->createOne();

    Feature::activate(FeatureConstants::ALLOW_DISCORD_NOTIFICATIONS);
    Bus::fake(SendDiscordNotificationJob::class);
    Event::fakeExcept(SeriesDeleted::class);

    $series->delete();

    Bus::assertDispatched(SendDiscordNotificationJob::class);
});

test('series restored sends discord notification', function () {
    $series = Series::factory()->createOne();

    Feature::activate(FeatureConstants::ALLOW_DISCORD_NOTIFICATIONS);
    Bus::fake(SendDiscordNotificationJob::class);
    Event::fakeExcept(SeriesRestored::class);

    $series->restore();

    Bus::assertDispatched(SendDiscordNotificationJob::class);
});

test('series updated sends discord notification', function () {
    $series = Series::factory()->createOne();

    Feature::activate(FeatureConstants::ALLOW_DISCORD_NOTIFICATIONS);
    Bus::fake(SendDiscordNotificationJob::class);
    Event::fakeExcept(SeriesUpdated::class);

    $changes = Series::factory()->makeOne();

    $series->fill($changes->getAttributes());
    $series->save();

    Bus::assertDispatched(SendDiscordNotificationJob::class);
});
