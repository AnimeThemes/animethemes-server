<?php

declare(strict_types=1);

namespace Jobs\Pivot;

use App\Jobs\SendDiscordNotification;
use App\Models\Artist;
use App\Models\Song;
use App\Pivots\ArtistSong;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\Config;
use Tests\TestCase;

/**
 * Class ArtistSongTest
 * @package Jobs\Pivot
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

        Config::set('app.allow_discord_notifications', true);
        Bus::fake(SendDiscordNotification::class);

        $artist->songs()->attach($song);

        Bus::assertDispatched(SendDiscordNotification::class);
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

        Config::set('app.allow_discord_notifications', true);
        Bus::fake(SendDiscordNotification::class);

        $artist->songs()->detach($song);

        Bus::assertDispatched(SendDiscordNotification::class);
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

        Config::set('app.allow_discord_notifications', true);
        Bus::fake(SendDiscordNotification::class);

        $artistSong->fill($changes->getAttributes());
        $artistSong->save();

        Bus::assertDispatched(SendDiscordNotification::class);
    }
}
