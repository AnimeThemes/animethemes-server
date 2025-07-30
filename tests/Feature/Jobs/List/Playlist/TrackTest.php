<?php

declare(strict_types=1);

use App\Constants\FeatureConstants;
use App\Events\List\Playlist\Track\TrackCreated;
use App\Events\List\Playlist\Track\TrackDeleted;
use App\Events\List\Playlist\Track\TrackUpdated;
use App\Jobs\SendDiscordNotificationJob;
use App\Models\List\Playlist;
use App\Models\List\Playlist\PlaylistTrack;
use App\Models\Wiki\Video;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\Event;
use Laravel\Pennant\Feature;

test('playlist created sends discord notification', function () {
    $playlist = Playlist::factory()->createOne();

    Feature::activate(FeatureConstants::ALLOW_DISCORD_NOTIFICATIONS);
    Bus::fake(SendDiscordNotificationJob::class);
    Event::fakeExcept(TrackCreated::class);

    PlaylistTrack::factory()
        ->for($playlist)
        ->createOne();

    Bus::assertNotDispatched(SendDiscordNotificationJob::class);
});

test('playlist deleted sends discord notification', function () {
    $playlist = Playlist::factory()->createOne();

    $track = PlaylistTrack::factory()
        ->for($playlist)
        ->createOne();

    Feature::activate(FeatureConstants::ALLOW_DISCORD_NOTIFICATIONS);
    Bus::fake(SendDiscordNotificationJob::class);
    Event::fakeExcept(TrackDeleted::class);

    $track->delete();

    Bus::assertNotDispatched(SendDiscordNotificationJob::class);
});

test('playlist updated sends discord notification', function () {
    $playlist = Playlist::factory()->createOne();

    $track = PlaylistTrack::factory()
        ->for($playlist)
        ->createOne();

    Feature::activate(FeatureConstants::ALLOW_DISCORD_NOTIFICATIONS);
    Bus::fake(SendDiscordNotificationJob::class);
    Event::fakeExcept(TrackUpdated::class);

    $changes = array_merge(
        PlaylistTrack::factory()->raw(),
        [PlaylistTrack::ATTRIBUTE_VIDEO => Video::factory()->createOne()->getKey()],
    );

    $track->fill($changes);
    $track->save();

    Bus::assertNotDispatched(SendDiscordNotificationJob::class);
});
