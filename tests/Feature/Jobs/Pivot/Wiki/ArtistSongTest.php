<?php

declare(strict_types=1);

use App\Constants\FeatureConstants;
use App\Events\Pivot\Wiki\ArtistSong\ArtistSongCreated;
use App\Events\Pivot\Wiki\ArtistSong\ArtistSongDeleted;
use App\Events\Pivot\Wiki\ArtistSong\ArtistSongUpdated;
use App\Jobs\SendDiscordNotificationJob;
use App\Models\Wiki\Artist;
use App\Models\Wiki\Song;
use App\Pivots\Wiki\ArtistSong;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\Event;
use Laravel\Pennant\Feature;

test('artist song created sends discord notification', function () {
    $artist = Artist::factory()->createOne();
    $song = Song::factory()->createOne();

    Feature::activate(FeatureConstants::ALLOW_DISCORD_NOTIFICATIONS);
    Bus::fake(SendDiscordNotificationJob::class);
    Event::fakeExcept(ArtistSongCreated::class);

    $artist->songs()->attach($song);

    Bus::assertDispatched(SendDiscordNotificationJob::class);
});

test('artist song deleted sends discord notification', function () {
    $artist = Artist::factory()->createOne();
    $song = Song::factory()->createOne();

    $artist->songs()->attach($song);

    Feature::activate(FeatureConstants::ALLOW_DISCORD_NOTIFICATIONS);
    Bus::fake(SendDiscordNotificationJob::class);
    Event::fakeExcept(ArtistSongDeleted::class);

    $artist->songs()->detach($song);

    Bus::assertDispatched(SendDiscordNotificationJob::class);
});

test('artist song updated sends discord notification', function () {
    $artist = Artist::factory()->createOne();
    $song = Song::factory()->createOne();

    $artistSong = ArtistSong::factory()
        ->for($artist, 'artist')
        ->for($song, 'song')
        ->createOne();

    $changes = ArtistSong::factory()
        ->for($artist, 'artist')
        ->for($song, 'song')
        ->makeOne();

    Feature::activate(FeatureConstants::ALLOW_DISCORD_NOTIFICATIONS);
    Bus::fake(SendDiscordNotificationJob::class);
    Event::fakeExcept(ArtistSongUpdated::class);

    $artistSong->fill($changes->getAttributes());
    $artistSong->save();

    Bus::assertDispatched(SendDiscordNotificationJob::class);
});
