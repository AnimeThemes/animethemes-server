<?php

declare(strict_types=1);

namespace Events\Pivot;

use App\Events\Pivot\ArtistSong\ArtistSongCreated;
use App\Events\Pivot\ArtistSong\ArtistSongDeleted;
use App\Events\Pivot\ArtistSong\ArtistSongUpdated;
use App\Models\Artist;
use App\Models\Song;
use App\Pivots\ArtistSong;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Event;
use Tests\TestCase;

/**
 * Class ArtistSongTest.
 */
class ArtistSongTest extends TestCase
{
    use RefreshDatabase;

    /**
     * When an Artist is attached to a Song or vice versa, an ArtistSongCreated event shall be dispatched.
     *
     * @return void
     */
    public function testArtistSongCreatedEventDispatched()
    {
        Event::fake();

        $artist = Artist::factory()->create();
        $song = Song::factory()->create();

        $artist->songs()->attach($song);

        Event::assertDispatched(ArtistSongCreated::class);
    }

    /**
     * When an Artist is detached from a Song or vice versa, an ArtistSongDeleted event shall be dispatched.
     *
     * @return void
     */
    public function testArtistSongDeletedEventDispatched()
    {
        Event::fake();

        $artist = Artist::factory()->create();
        $song = Song::factory()->create();

        $artist->songs()->attach($song);
        $artist->songs()->detach($song);

        Event::assertDispatched(ArtistSongDeleted::class);
    }

    /**
     * When an Artist Song pivot is updated, an ArtistSongUpdated event shall be dispatched.
     *
     * @return void
     */
    public function testArtistSongUpdatedEventDispatched()
    {
        Event::fake();

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

        $artistSong->fill($changes->getAttributes());
        $artistSong->save();

        Event::assertDispatched(ArtistSongUpdated::class);
    }
}
