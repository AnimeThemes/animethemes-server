<?php

declare(strict_types=1);

use App\Constants\FeatureConstants;
use App\Events\Pivot\Wiki\SongResource\SongResourceCreated;
use App\Events\Pivot\Wiki\SongResource\SongResourceDeleted;
use App\Events\Pivot\Wiki\SongResource\SongResourceUpdated;
use App\Jobs\SendDiscordNotificationJob;
use App\Models\Wiki\ExternalResource;
use App\Models\Wiki\Song;
use App\Pivots\Wiki\SongResource;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\Event;
use Laravel\Pennant\Feature;

test('song resource created sends discord notification', function () {
    $song = Song::factory()->createOne();
    $resource = ExternalResource::factory()->createOne();

    Feature::activate(FeatureConstants::ALLOW_DISCORD_NOTIFICATIONS);
    Bus::fake(SendDiscordNotificationJob::class);
    Event::fakeExcept(SongResourceCreated::class);

    $song->resources()->attach($resource);

    Bus::assertDispatched(SendDiscordNotificationJob::class);
});

test('song resource deleted sends discord notification', function () {
    $song = Song::factory()->createOne();
    $resource = ExternalResource::factory()->createOne();

    $song->resources()->attach($resource);

    Feature::activate(FeatureConstants::ALLOW_DISCORD_NOTIFICATIONS);
    Bus::fake(SendDiscordNotificationJob::class);
    Event::fakeExcept(SongResourceDeleted::class);

    $song->resources()->detach($resource);

    Bus::assertDispatched(SendDiscordNotificationJob::class);
});

test('song resource updated sends discord notification', function () {
    $song = Song::factory()->createOne();
    $resource = ExternalResource::factory()->createOne();

    $songResource = SongResource::factory()
        ->for($song, 'song')
        ->for($resource, 'resource')
        ->createOne();

    $changes = SongResource::factory()
        ->for($song, 'song')
        ->for($resource, 'resource')
        ->makeOne();

    Feature::activate(FeatureConstants::ALLOW_DISCORD_NOTIFICATIONS);
    Bus::fake(SendDiscordNotificationJob::class);
    Event::fakeExcept(SongResourceUpdated::class);

    $songResource->fill($changes->getAttributes());
    $songResource->save();

    Bus::assertDispatched(SendDiscordNotificationJob::class);
});
