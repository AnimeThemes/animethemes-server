<?php

declare(strict_types=1);

use App\Constants\FeatureConstants;
use App\Events\Admin\Feature\FeatureCreated;
use App\Events\Admin\Feature\FeatureDeleted;
use App\Events\Admin\Feature\FeatureUpdated;
use App\Jobs\SendDiscordNotificationJob;
use App\Models\Admin\Feature as FeatureModel;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\Event;
use Laravel\Pennant\Feature;

test('feature created sends discord notification', function () {
    Feature::activate(FeatureConstants::ALLOW_DISCORD_NOTIFICATIONS);
    Bus::fake(SendDiscordNotificationJob::class);
    Event::fakeExcept(FeatureCreated::class);

    FeatureModel::factory()->createOne();

    Bus::assertDispatched(SendDiscordNotificationJob::class);
});

test('feature deleted sends discord notification', function () {
    $feature = FeatureModel::factory()->createOne();

    Feature::activate(FeatureConstants::ALLOW_DISCORD_NOTIFICATIONS);
    Bus::fake(SendDiscordNotificationJob::class);
    Event::fakeExcept(FeatureDeleted::class);

    $feature->delete();

    Bus::assertDispatched(SendDiscordNotificationJob::class);
});

test('feature updated sends discord notification', function () {
    $feature = FeatureModel::factory()->createOne();

    Feature::activate(FeatureConstants::ALLOW_DISCORD_NOTIFICATIONS);
    Bus::fake(SendDiscordNotificationJob::class);
    Event::fakeExcept(FeatureUpdated::class);

    $feature->update([
        FeatureModel::ATTRIBUTE_VALUE => ! $feature->value,
    ]);

    Bus::assertDispatched(SendDiscordNotificationJob::class);
});
