<?php

declare(strict_types=1);

namespace Tests\Feature\Jobs\Pivot\Wiki;

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
use Tests\TestCase;

/**
 * Class ArtistSongTest.
 */
class ArtistSongTest extends TestCase
{
    /**
     * When an Artist is attached to a Song or vice versa, a SendDiscordNotification job shall be dispatched.
     *
     * @return void
     */
    public function testArtistSongCreatedSendsDiscordNotification(): void
    {
        $artist = Artist::factory()->createOne();
        $song = Song::factory()->createOne();

        Feature::activate(FeatureConstants::ALLOW_DISCORD_NOTIFICATIONS);
        Bus::fake(SendDiscordNotificationJob::class);
        Event::fakeExcept(ArtistSongCreated::class);

        $artist->songs()->attach($song);

        Bus::assertDispatched(SendDiscordNotificationJob::class);
    }

    /**
     * When an Artist is detached from a Song or vice versa, a SendDiscordNotification job shall be dispatched.
     *
     * @return void
     */
    public function testArtistSongDeletedSendsDiscordNotification(): void
    {
        $artist = Artist::factory()->createOne();
        $song = Song::factory()->createOne();

        $artist->songs()->attach($song);

        Feature::activate(FeatureConstants::ALLOW_DISCORD_NOTIFICATIONS);
        Bus::fake(SendDiscordNotificationJob::class);
        Event::fakeExcept(ArtistSongDeleted::class);

        $artist->songs()->detach($song);

        Bus::assertDispatched(SendDiscordNotificationJob::class);
    }

    /**
     * When an Artist Song pivot is updated, a SendDiscordNotification job shall be dispatched.
     *
     * @return void
     */
    public function testArtistSongUpdatedSendsDiscordNotification(): void
    {
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
    }
}
