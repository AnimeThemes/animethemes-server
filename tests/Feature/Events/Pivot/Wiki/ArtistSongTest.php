<?php

declare(strict_types=1);

namespace Tests\Feature\Events\Pivot\Wiki;

use App\Events\Pivot\Wiki\ArtistSong\ArtistSongCreated;
use App\Events\Pivot\Wiki\ArtistSong\ArtistSongDeleted;
use App\Events\Pivot\Wiki\ArtistSong\ArtistSongUpdated;
use App\Models\Wiki\Artist;
use App\Models\Wiki\Song;
use App\Pivots\Wiki\ArtistSong;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Event;
use Tests\TestCase;

/**
 * Class ArtistSongTest.
 */
class ArtistSongTest extends TestCase
{
    /**
     * When an Artist is attached to a Song or vice versa, an ArtistSongCreated event shall be dispatched.
     *
     * @return void
     */
    public function testArtistSongCreatedEventDispatched(): void
    {
        $artist = Artist::factory()->createOne();
        $song = Song::factory()->createOne();

        $artist->songs()->attach($song);

        Event::assertDispatched(ArtistSongCreated::class);
    }

    /**
     * When an Artist is detached from a Song or vice versa, an ArtistSongDeleted event shall be dispatched.
     *
     * @return void
     */
    public function testArtistSongDeletedEventDispatched(): void
    {
        $artist = Artist::factory()->createOne();
        $song = Song::factory()->createOne();

        $artist->songs()->attach($song);
        $artist->songs()->detach($song);

        Event::assertDispatched(ArtistSongDeleted::class);
    }

    /**
     * When an Artist Song pivot is updated, an ArtistSongUpdated event shall be dispatched.
     *
     * @return void
     */
    public function testArtistSongUpdatedEventDispatched(): void
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

        $artistSong->fill($changes->getAttributes());
        $artistSong->save();

        Event::assertDispatched(ArtistSongUpdated::class);
    }

    /**
     * The ArtistSongUpdated event shall contain embed fields.
     *
     * @return void
     */
    public function testArtistSongUpdatedEventEmbedFields(): void
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

        $artistSong->fill($changes->getAttributes());
        $artistSong->save();

        Event::assertDispatched(ArtistSongUpdated::class, function (ArtistSongUpdated $event) {
            $message = $event->getDiscordMessage();

            return ! empty(Arr::get($message->embed, 'fields'));
        });
    }
}
