<?php

namespace Tests\Unit\Pivots;

use App\Models\Artist;
use App\Models\Song;
use App\Pivots\ArtistSong;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class ArtistSongTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    /**
     * An ArtistSong shall belong to an Artist.
     *
     * @return void
     */
    public function testArtist()
    {
        $artist_song = ArtistSong::factory()
            ->for(Artist::factory())
            ->for(Song::factory())
            ->create();

        $this->assertInstanceOf(BelongsTo::class, $artist_song->artist());
        $this->assertInstanceOf(Artist::class, $artist_song->artist()->first());
    }

    /**
     * An ArtistSong shall belong to a Song.
     *
     * @return void
     */
    public function testSong()
    {
        $artist_song = ArtistSong::factory()
            ->for(Artist::factory())
            ->for(Song::factory())
            ->create();

        $this->assertInstanceOf(BelongsTo::class, $artist_song->song());
        $this->assertInstanceOf(Song::class, $artist_song->song()->first());
    }
}
