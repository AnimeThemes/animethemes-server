<?php

declare(strict_types=1);

namespace Models\Wiki;

use App\Models\Wiki\Anime;
use App\Models\Wiki\Artist;
use App\Models\Wiki\Song;
use App\Models\Wiki\Theme;
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
        $song = Song::factory()->create();

        static::assertIsString($song->searchableAs());
    }

    /**
     * Song shall be a searchable resource.
     *
     * @return void
     */
    public function testToSearchableArray()
    {
        $song = Song::factory()->create();

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

        $song = Song::factory()->create();

        static::assertEquals(1, $song->audits->count());
    }

    /**
     * Songs shall be nameable.
     *
     * @return void
     */
    public function testNameable()
    {
        $song = Song::factory()->create();

        static::assertIsString($song->getName());
    }

    /**
     * Song shall have a one-to-many relationship with the type Theme.
     *
     * @return void
     */
    public function testThemes()
    {
        $themeCount = $this->faker->randomDigitNotNull;

        $song = Song::factory()
            ->has(Theme::factory()->for(Anime::factory())->count($themeCount))
            ->create();

        static::assertInstanceOf(HasMany::class, $song->themes());
        static::assertEquals($themeCount, $song->themes()->count());
        static::assertInstanceOf(Theme::class, $song->themes()->first());
    }

    /**
     * Song shall have a many-to-many relationship with the type Artist.
     *
     * @return void
     */
    public function testArtists()
    {
        $artistCount = $this->faker->randomDigitNotNull;

        $song = Song::factory()
            ->has(Artist::factory()->count($artistCount))
            ->create();

        static::assertInstanceOf(BelongsToMany::class, $song->artists());
        static::assertEquals($artistCount, $song->artists()->count());
        static::assertInstanceOf(Artist::class, $song->artists()->first());
        static::assertEquals(ArtistSong::class, $song->artists()->getPivotClass());
    }
}
