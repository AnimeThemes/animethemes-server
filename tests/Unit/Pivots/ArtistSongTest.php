<?php declare(strict_types=1);

namespace Pivots;

use App\Models\Artist;
use App\Models\Song;
use App\Pivots\ArtistSong;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithoutEvents;
use Tests\TestCase;

/**
 * Class ArtistSongTest
 * @package Pivots
 */
class ArtistSongTest extends TestCase
{
    use RefreshDatabase;
    use WithoutEvents;

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

        static::assertInstanceOf(BelongsTo::class, $artistSong->artist());
        static::assertInstanceOf(Artist::class, $artistSong->artist()->first());
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

        static::assertInstanceOf(BelongsTo::class, $artistSong->song());
        static::assertInstanceOf(Song::class, $artistSong->song()->first());
    }
}
