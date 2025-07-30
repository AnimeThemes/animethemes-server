<?php

declare(strict_types=1);

use App\Constants\FeatureConstants;
use App\Events\Pivot\Wiki\AnimeStudio\AnimeStudioCreated;
use App\Events\Pivot\Wiki\AnimeStudio\AnimeStudioDeleted;
use App\Jobs\SendDiscordNotificationJob;
use App\Models\Wiki\Anime;
use App\Models\Wiki\Studio;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\Event;
use Laravel\Pennant\Feature;

test('anime studio created sends discord notification', function () {
    $anime = Anime::factory()->createOne();
    $studio = Studio::factory()->createOne();

    Feature::activate(FeatureConstants::ALLOW_DISCORD_NOTIFICATIONS);
    Bus::fake(SendDiscordNotificationJob::class);
    Event::fakeExcept(AnimeStudioCreated::class);

    $anime->studios()->attach($studio);

    Bus::assertDispatched(SendDiscordNotificationJob::class);
});

test('anime studio deleted sends discord notification', function () {
    $anime = Anime::factory()->createOne();
    $studio = Studio::factory()->createOne();

    $anime->studios()->attach($studio);

    Feature::activate(FeatureConstants::ALLOW_DISCORD_NOTIFICATIONS);
    Bus::fake(SendDiscordNotificationJob::class);
    Event::fakeExcept(AnimeStudioDeleted::class);

    $anime->studios()->detach($studio);

    Bus::assertDispatched(SendDiscordNotificationJob::class);
});
