<?php

declare(strict_types=1);

use App\Constants\FeatureConstants;
use App\Events\Wiki\Synonym\SynonymCreated;
use App\Events\Wiki\Synonym\SynonymDeleted;
use App\Events\Wiki\Synonym\SynonymRestored;
use App\Events\Wiki\Synonym\SynonymUpdated;
use App\Jobs\SendDiscordNotificationJob;
use App\Models\Wiki\Anime;
use App\Models\Wiki\Synonym;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\Event;
use Laravel\Pennant\Feature;

test('synonym created sends discord notification', function () {
    $anime = Anime::factory()->createOne();

    Feature::activate(FeatureConstants::ALLOW_DISCORD_NOTIFICATIONS);
    Bus::fake(SendDiscordNotificationJob::class);
    Event::fakeExcept(SynonymCreated::class);

    Synonym::factory()->forAnime()->createOne();

    Bus::assertDispatched(SendDiscordNotificationJob::class);
});

test('synonym deleted sends discord notification', function () {
    $synonym = Synonym::factory()->forAnime()->createOne();

    Feature::activate(FeatureConstants::ALLOW_DISCORD_NOTIFICATIONS);
    Bus::fake(SendDiscordNotificationJob::class);
    Event::fakeExcept(SynonymDeleted::class);

    $synonym->delete();

    Bus::assertDispatched(SendDiscordNotificationJob::class);
});

test('synonym restored sends discord notification', function () {
    $synonym = Synonym::factory()->forAnime()->createOne();

    Feature::activate(FeatureConstants::ALLOW_DISCORD_NOTIFICATIONS);
    Bus::fake(SendDiscordNotificationJob::class);
    Event::fakeExcept(SynonymRestored::class);

    $synonym->restore();

    Bus::assertDispatched(SendDiscordNotificationJob::class);
});

test('synonym updated sends discord notification', function () {
    $synonym = Synonym::factory()->forAnime()->createOne();

    $changes = Synonym::factory()->forAnime()->makeOne();

    Feature::activate(FeatureConstants::ALLOW_DISCORD_NOTIFICATIONS);
    Bus::fake(SendDiscordNotificationJob::class);
    Event::fakeExcept(SynonymUpdated::class);

    $synonym->fill($changes->getAttributes());
    $synonym->save();

    Bus::assertDispatched(SendDiscordNotificationJob::class);
});
