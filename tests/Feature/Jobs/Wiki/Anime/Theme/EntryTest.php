<?php

declare(strict_types=1);

use App\Constants\FeatureConstants;
use App\Events\Wiki\Entry\EntryCreated;
use App\Events\Wiki\Entry\EntryDeleted;
use App\Events\Wiki\Entry\EntryRestored;
use App\Events\Wiki\Entry\EntryUpdated;
use App\Jobs\SendDiscordNotificationJob;
use App\Models\Wiki\Anime;
use App\Models\Wiki\Entry;
use App\Models\Wiki\Theme;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\Event;
use Laravel\Pennant\Feature;

test('entry created sends discord notification', function (): void {
    $theme = Theme::factory()
        ->for(Anime::factory())
        ->createOne();

    Feature::activate(FeatureConstants::ALLOW_DISCORD_NOTIFICATIONS);
    Bus::fake(SendDiscordNotificationJob::class);
    Event::fakeExcept(EntryCreated::class);

    Entry::factory()->for($theme)->createOne();

    Bus::assertDispatched(SendDiscordNotificationJob::class);
});

test('entry deleted sends discord notification', function (): void {
    $entry = Entry::factory()
        ->for(Theme::factory()->for(Anime::factory()))
        ->createOne();

    Feature::activate(FeatureConstants::ALLOW_DISCORD_NOTIFICATIONS);
    Bus::fake(SendDiscordNotificationJob::class);
    Event::fakeExcept(EntryDeleted::class);

    $entry->delete();

    Bus::assertDispatched(SendDiscordNotificationJob::class);
});

test('entry restored sends discord notification', function (): void {
    $entry = Entry::factory()
        ->for(Theme::factory()->for(Anime::factory()))
        ->createOne();

    Feature::activate(FeatureConstants::ALLOW_DISCORD_NOTIFICATIONS);
    Bus::fake(SendDiscordNotificationJob::class);
    Event::fakeExcept(EntryRestored::class);

    $entry->restore();

    Bus::assertDispatched(SendDiscordNotificationJob::class);
});

test('entry updated sends discord notification', function (): void {
    $entry = Entry::factory()
        ->for(Theme::factory()->for(Anime::factory()))
        ->createOne();

    $changes = Entry::factory()
        ->for(Theme::factory()->for(Anime::factory()))
        ->makeOne();

    Feature::activate(FeatureConstants::ALLOW_DISCORD_NOTIFICATIONS);
    Bus::fake(SendDiscordNotificationJob::class);
    Event::fakeExcept(EntryUpdated::class);

    $entry->fill($changes->getAttributes());
    $entry->save();

    Bus::assertDispatched(SendDiscordNotificationJob::class);
});
