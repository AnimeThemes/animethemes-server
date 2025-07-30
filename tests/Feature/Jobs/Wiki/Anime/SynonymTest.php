<?php

declare(strict_types=1);

use App\Constants\FeatureConstants;
use App\Events\Wiki\Anime\Synonym\SynonymCreated;
use App\Events\Wiki\Anime\Synonym\SynonymDeleted;
use App\Events\Wiki\Anime\Synonym\SynonymRestored;
use App\Events\Wiki\Anime\Synonym\SynonymUpdated;
use App\Jobs\SendDiscordNotificationJob;
use App\Models\Wiki\Anime;
use App\Models\Wiki\Anime\AnimeSynonym;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\Event;
use Laravel\Pennant\Feature;

test('synonym created sends discord notification', function () {
    $anime = Anime::factory()->createOne();

    Feature::activate(FeatureConstants::ALLOW_DISCORD_NOTIFICATIONS);
    Bus::fake(SendDiscordNotificationJob::class);
    Event::fakeExcept(SynonymCreated::class);

    AnimeSynonym::factory()->for($anime)->createOne();

    Bus::assertDispatched(SendDiscordNotificationJob::class);
});

test('synonym deleted sends discord notification', function () {
    $synonym = AnimeSynonym::factory()
        ->for(Anime::factory())
        ->createOne();

    Feature::activate(FeatureConstants::ALLOW_DISCORD_NOTIFICATIONS);
    Bus::fake(SendDiscordNotificationJob::class);
    Event::fakeExcept(SynonymDeleted::class);

    $synonym->delete();

    Bus::assertDispatched(SendDiscordNotificationJob::class);
});

test('synonym restored sends discord notification', function () {
    $synonym = AnimeSynonym::factory()
        ->for(Anime::factory())
        ->createOne();

    Feature::activate(FeatureConstants::ALLOW_DISCORD_NOTIFICATIONS);
    Bus::fake(SendDiscordNotificationJob::class);
    Event::fakeExcept(SynonymRestored::class);

    $synonym->restore();

    Bus::assertDispatched(SendDiscordNotificationJob::class);
});

test('synonym updated sends discord notification', function () {
    $synonym = AnimeSynonym::factory()
        ->for(Anime::factory())
        ->createOne();

    $changes = AnimeSynonym::factory()
        ->for(Anime::factory())
        ->makeOne();

    Feature::activate(FeatureConstants::ALLOW_DISCORD_NOTIFICATIONS);
    Bus::fake(SendDiscordNotificationJob::class);
    Event::fakeExcept(SynonymUpdated::class);

    $synonym->fill($changes->getAttributes());
    $synonym->save();

    Bus::assertDispatched(SendDiscordNotificationJob::class);
});
