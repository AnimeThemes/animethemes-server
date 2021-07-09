<?php

declare(strict_types=1);

namespace Tests\Feature\Jobs\Pivot;

use App\Jobs\SendDiscordNotificationJob;
use App\Models\Wiki\Artist;
use App\Models\Wiki\Song;
use App\Pivots\ArtistSong;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\Config;
use Tests\TestCase;

/**
 * Class ArtistSongTest.
 */
class ArtistSongTest extends TestCase
{
    use RefreshDatabase;

    /**
     * When an Artist is attached to a Song or vice versa, a SendDiscordNotification job shall be dispatched.
     *
     * @return void
     */
    public function testArtistSongCreatedSendsDiscordNotification()
    {
        $artist = Artist::factory()->create();
        $song = Song::factory()->create();

        Config::set('flags.allow_discord_notifications', true);
        Bus::fake(SendDiscordNotificationJob::class);

        $artist->songs()->attach($song);

        Bus::assertDispatched(SendDiscordNotificationJob::class);
    }

    /**
     * When an Artist is detached from a Song or vice versa, a SendDiscordNotification job shall be dispatched.
     *
     * @return void
     */
    public function testArtistSongDeletedSendsDiscordNotification()
    {
        $artist = Artist::factory()->create();
        $song = Song::factory()->create();
        $artist->songs()->attach($song);

        Config::set('flags.allow_discord_notifications', true);
        Bus::fake(SendDiscordNotificationJob::class);

        $artist->songs()->detach($song);

        Bus::assertDispatched(SendDiscordNotificationJob::class);
    }

    /**
     * When an Artist Song pivot is updated, a SendDiscordNotification job shall be dispatched.
     *
     * @return void
     */
    public function testArtistSongUpdatedSendsDiscordNotification()
    {
        $artist = Artist::factory()->create();
        $song = Song::factory()->create();

        $artistSong = ArtistSong::factory()
            ->for($artist, 'artist')
            ->for($song, 'song')
            ->create();

        $changes = ArtistSong::factory()
            ->for($artist, 'artist')
            ->for($song, 'song')
            ->make();

        Config::set('flags.allow_discord_notifications', true);
        Bus::fake(SendDiscordNotificationJob::class);

        $artistSong->fill($changes->getAttributes());
        $artistSong->save();

        Bus::assertDispatched(SendDiscordNotificationJob::class);
    }
}