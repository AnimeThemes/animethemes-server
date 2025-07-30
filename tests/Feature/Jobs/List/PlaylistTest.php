<?php

declare(strict_types=1);

use App\Constants\FeatureConstants;
use App\Events\List\Playlist\PlaylistCreated;
use App\Events\List\Playlist\PlaylistDeleted;
use App\Events\List\Playlist\PlaylistUpdated;
use App\Jobs\SendDiscordNotificationJob;
use App\Models\List\Playlist;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\Event;
use Laravel\Pennant\Feature;

test('playlist created sends discord notification', function () {
    Feature::activate(FeatureConstants::ALLOW_DISCORD_NOTIFICATIONS);
    Bus::fake(SendDiscordNotificationJob::class);
    Event::fakeExcept(PlaylistCreated::class);

    Playlist::factory()->createOne();

    Bus::assertDispatched(SendDiscordNotificationJob::class);
});

test('playlist deleted sends discord notification', function () {
    $playlist = Playlist::factory()->createOne();

    Feature::activate(FeatureConstants::ALLOW_DISCORD_NOTIFICATIONS);
    Bus::fake(SendDiscordNotificationJob::class);
    Event::fakeExcept(PlaylistDeleted::class);

    $playlist->delete();

    Bus::assertNotDispatched(SendDiscordNotificationJob::class);
});

test('playlist updated sends discord notification', function () {
    $playlist = Playlist::factory()->createOne();

    Feature::activate(FeatureConstants::ALLOW_DISCORD_NOTIFICATIONS);
    Bus::fake(SendDiscordNotificationJob::class);
    Event::fakeExcept(PlaylistUpdated::class);

    $changes = Playlist::factory()->makeOne();

    $playlist->fill($changes->getAttributes());
    $playlist->save();

    Bus::assertNotDispatched(SendDiscordNotificationJob::class);
});
