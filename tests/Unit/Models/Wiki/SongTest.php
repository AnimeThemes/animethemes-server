<?php

declare(strict_types=1);

namespace Tests\Unit\Models\Wiki;

use App\Models\Wiki\Anime;
use App\Models\Wiki\Anime\AnimeTheme;
use App\Models\Wiki\Artist;
use App\Models\Wiki\Song;
use App\Pivots\ArtistSong;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

/**
 * Class SongTest.
 */
class SongTest extends TestCase
{
    use WithFaker;

    /**
     * Song shall be a searchable resource.
     *
     * @return void
     */
    public function testSearchableAs(): void
    {
        $song = Song::factory()->createOne();

        static::assertIsString($song->searchableAs());
    }

    /**
     * Song shall be a searchable resource.
     *
     * @return void
     */
    public function testToSearchableArray(): void
    {
        $song = Song::factory()->createOne();

        static::assertIsArray($song->toSearchableArray());
    }

    /**
     * Songs shall be nameable.
     *
     * @return void
     */
    public function testNameable(): void
    {
        $song = Song::factory()->createOne();

        static::assertIsString($song->getName());
    }

    /**
     * Song shall have a one-to-many relationship with the type Theme.
     *
     * @return void
     */
    public function testThemes(): void
    {
        $themeCount = $this->faker->randomDigitNotNull();

        $song = Song::factory()
            ->has(AnimeTheme::factory()->for(Anime::factory())->count($themeCount))
            ->createOne();

        static::assertInstanceOf(HasMany::class, $song->animethemes());
        static::assertEquals($themeCount, $song->animethemes()->count());
        static::assertInstanceOf(AnimeTheme::class, $song->animethemes()->first());
    }

    /**
     * Song shall have a many-to-many relationship with the type Artist.
     *
     * @return void
     */
    public function testArtists(): void
    {
        $artistCount = $this->faker->randomDigitNotNull();

        $song = Song::factory()
            ->has(Artist::factory()->count($artistCount))
            ->createOne();

        static::assertInstanceOf(BelongsToMany::class, $song->artists());
        static::assertEquals($artistCount, $song->artists()->count());
        static::assertInstanceOf(Artist::class, $song->artists()->first());
        static::assertEquals(ArtistSong::class, $song->artists()->getPivotClass());
    }
}
