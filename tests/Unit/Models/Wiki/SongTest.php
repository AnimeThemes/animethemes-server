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
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Config;
use Tests\TestCase;

/**
 * Class SongTest.
 */
class SongTest extends TestCase
{
    use RefreshDatabase;
    use WithFaker;

    /**
     * Song shall be a searchable resource.
     *
     * @return void
     */
    public function testSearchableAs()
    {
        $song = Song::factory()->createOne();

        static::assertIsString($song->searchableAs());
    }

    /**
     * Song shall be a searchable resource.
     *
     * @return void
     */
    public function testToSearchableArray()
    {
        $song = Song::factory()->createOne();

        static::assertIsArray($song->toSearchableArray());
    }

    /**
     * Songs shall be auditable.
     *
     * @return void
     */
    public function testAuditable()
    {
        Config::set('audit.console', true);

        $song = Song::factory()->createOne();

        static::assertEquals(1, $song->audits()->count());
    }

    /**
     * Songs shall be nameable.
     *
     * @return void
     */
    public function testNameable()
    {
        $song = Song::factory()->createOne();

        static::assertIsString($song->getName());
    }

    /**
     * Song shall have a one-to-many relationship with the type Theme.
     *
     * @return void
     */
    public function testThemes()
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
    public function testArtists()
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
