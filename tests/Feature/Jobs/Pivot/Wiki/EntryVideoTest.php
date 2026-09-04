<?php

declare(strict_types=1);

use App\Constants\FeatureConstants;
use App\Events\Pivot\Wiki\EntryVideo\EntryVideoCreated;
use App\Events\Pivot\Wiki\EntryVideo\EntryVideoDeleted;
use App\Jobs\SendDiscordNotificationJob;
use App\Models\Wiki\Anime;
use App\Models\Wiki\Entry;
use App\Models\Wiki\Theme;
use App\Models\Wiki\Video;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\Event;
use Laravel\Pennant\Feature;

test('anime theme entry video created sends discord notification', function (): void {
    $video = Video::factory()->createOne();
    $entry = Entry::factory()
        ->for(Theme::factory()->for(Anime::factory()))
        ->createOne();

    Feature::activate(FeatureConstants::ALLOW_DISCORD_NOTIFICATIONS);
    Bus::fake(SendDiscordNotificationJob::class);
    Event::fakeExcept(EntryVideoCreated::class);

    $video->animethemeentries()->attach($entry);

    Bus::assertDispatched(SendDiscordNotificationJob::class);
});

test('anime theme entry video deleted sends discord notification', function (): void {
    $video = Video::factory()->createOne();
    $entry = Entry::factory()
        ->for(Theme::factory()->for(Anime::factory()))
        ->createOne();

    $video->animethemeentries()->attach($entry);

    Feature::activate(FeatureConstants::ALLOW_DISCORD_NOTIFICATIONS);
    Bus::fake(SendDiscordNotificationJob::class);
    Event::fakeExcept(EntryVideoDeleted::class);

    $video->animethemeentries()->detach($entry);

    Bus::assertDispatched(SendDiscordNotificationJob::class);
});
