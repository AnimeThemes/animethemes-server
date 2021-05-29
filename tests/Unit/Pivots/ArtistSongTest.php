<?php

namespace Tests\Unit\Pivots;

use App\Models\Artist;
use App\Models\Song;
use App\Pivots\ArtistSong;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithoutEvents;
use Tests\TestCase;

class ArtistSongTest extends TestCase
{
    use RefreshDatabase, WithoutEvents;

    /**
     * An ArtistSong shall belong to an Artist.
     *
     * @return void
     */
    public function testArtist()
    {
        $artistSong = ArtistSong::factory()
            ->for(Artist::factory())
            ->for(Song::factory())
            ->create();

        $this->assertInstanceOf(BelongsTo::class, $artistSong->artist());
        $this->assertInstanceOf(Artist::class, $artistSong->artist()->first());
    }

    /**
     * An ArtistSong shall belong to a Song.
     *
     * @return void
     */
    public function testSong()
    {
        $artistSong = ArtistSong::factory()
            ->for(Artist::factory())
            ->for(Song::factory())
            ->create();

        $this->assertInstanceOf(BelongsTo::class, $artistSong->song());
        $this->assertInstanceOf(Song::class, $artistSong->song()->first());
    }
}
