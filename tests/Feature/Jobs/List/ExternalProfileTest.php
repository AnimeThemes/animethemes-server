<?php

declare(strict_types=1);

use App\Constants\FeatureConstants;
use App\Events\List\ExternalProfile\ExternalProfileCreated;
use App\Events\List\ExternalProfile\ExternalProfileDeleted;
use App\Events\List\ExternalProfile\ExternalProfileUpdated;
use App\Jobs\SendDiscordNotificationJob;
use App\Models\List\ExternalProfile;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\Event;
use Laravel\Pennant\Feature;

test('external profile created sends discord notification', function () {
    Feature::activate(FeatureConstants::ALLOW_DISCORD_NOTIFICATIONS);
    Bus::fake(SendDiscordNotificationJob::class);
    Event::fakeExcept(ExternalProfileCreated::class);

    ExternalProfile::factory()->createOne();

    Bus::assertDispatched(SendDiscordNotificationJob::class);
});

test('external profile deleted sends discord notification', function () {
    $profile = ExternalProfile::factory()->createOne();

    Feature::activate(FeatureConstants::ALLOW_DISCORD_NOTIFICATIONS);
    Bus::fake(SendDiscordNotificationJob::class);
    Event::fakeExcept(ExternalProfileDeleted::class);

    $profile->delete();

    Bus::assertNotDispatched(SendDiscordNotificationJob::class);
});

test('external profile updated sends discord notification', function () {
    $profile = ExternalProfile::factory()->createOne();

    Feature::activate(FeatureConstants::ALLOW_DISCORD_NOTIFICATIONS);
    Bus::fake(SendDiscordNotificationJob::class);
    Event::fakeExcept(ExternalProfileUpdated::class);

    $changes = ExternalProfile::factory()->makeOne();

    $profile->fill($changes->getAttributes());
    $profile->save();

    Bus::assertNotDispatched(SendDiscordNotificationJob::class);
});
