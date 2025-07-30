<?php

declare(strict_types=1);

use App\Constants\FeatureConstants;
use App\Events\Pivot\List\PlaylistImage\PlaylistImageCreated;
use App\Events\Pivot\List\PlaylistImage\PlaylistImageDeleted;
use App\Jobs\SendDiscordNotificationJob;
use App\Models\List\Playlist;
use App\Models\Wiki\Image;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\Event;
use Laravel\Pennant\Feature;

test('playlist image created sends discord notification', function () {
    $playlist = Playlist::factory()->createOne();
    $image = Image::factory()->createOne();

    Feature::activate(FeatureConstants::ALLOW_DISCORD_NOTIFICATIONS);
    Bus::fake(SendDiscordNotificationJob::class);
    Event::fakeExcept(PlaylistImageCreated::class);

    $playlist->images()->attach($image);

    Bus::assertDispatched(SendDiscordNotificationJob::class);
});

test('playlist image deleted sends discord notification', function () {
    $playlist = Playlist::factory()->createOne();
    $image = Image::factory()->createOne();

    $playlist->images()->attach($image);

    Feature::activate(FeatureConstants::ALLOW_DISCORD_NOTIFICATIONS);
    Bus::fake(SendDiscordNotificationJob::class);
    Event::fakeExcept(PlaylistImageDeleted::class);

    $playlist->images()->detach($image);

    Bus::assertDispatched(SendDiscordNotificationJob::class);
});
