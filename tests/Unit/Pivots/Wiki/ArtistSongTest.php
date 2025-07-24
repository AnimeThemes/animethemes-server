<?php

declare(strict_types=1);

namespace Tests\Unit\Pivots\Wiki;

use App\Models\Wiki\Artist;
use App\Models\Wiki\Song;
use App\Pivots\Wiki\ArtistSong;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Tests\TestCase;

class ArtistSongTest extends TestCase
{
    /**
     * An ArtistSong shall belong to an Artist.
     */
    public function testArtist(): void
    {
        $artistSong = ArtistSong::factory()
            ->for(Artist::factory())
            ->for(Song::factory())
            ->createOne();

        static::assertInstanceOf(BelongsTo::class, $artistSong->artist());
        static::assertInstanceOf(Artist::class, $artistSong->artist()->first());
    }

    /**
     * An ArtistSong shall belong to a Song.
     */
    public function testSong(): void
    {
        $artistSong = ArtistSong::factory()
            ->for(Artist::factory())
            ->for(Song::factory())
            ->createOne();

        static::assertInstanceOf(BelongsTo::class, $artistSong->song());
        static::assertInstanceOf(Song::class, $artistSong->song()->first());
    }
}
