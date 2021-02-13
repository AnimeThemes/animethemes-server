<?php

namespace Tests\Unit\Models;

use App\Models\Anime;
use App\Models\Artist;
use App\Models\Song;
use App\Models\Theme;
use App\Pivots\ArtistSong;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Config;
use Tests\TestCase;

class SongTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    /**
     * Song shall be a searchable resource.
     *
     * @return void
     */
    public function testSearchableAs()
    {
        $song = Song::factory()->create();

        $this->assertIsString($song->searchableAs());
    }

    /**
     * Song shall be a searchable resource.
     *
     * @return void
     */
    public function testToSearchableArray()
    {
        $song = Song::factory()->create();

        $this->assertIsArray($song->toSearchableArray());
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

        $this->assertEquals(1, $song->audits->count());
    }

    /**
     * Songs shall be nameable.
     *
     * @return void
     */
    public function testNameable()
    {
        $song = Song::factory()->create();

        $this->assertIsString($song->getName());
    }

    /**
     * Song shall have a one-to-many relationship with the type Theme.
     *
     * @return void
     */
    public function testThemes()
    {
        $theme_count = $this->faker->randomDigitNotNull;

        $song = Song::factory()
            ->has(Theme::factory()->for(Anime::factory())->count($theme_count))
            ->create();

        $this->assertInstanceOf(HasMany::class, $song->themes());
        $this->assertEquals($theme_count, $song->themes()->count());
        $this->assertInstanceOf(Theme::class, $song->themes()->first());
    }

    /**
     * Song shall have a many-to-many relationship with the type Artist.
     *
     * @return void
     */
    public function testArtists()
    {
        $artist_count = $this->faker->randomDigitNotNull;

        $song = Song::factory()
            ->has(Artist::factory()->count($artist_count))
            ->create();

        $this->assertInstanceOf(BelongsToMany::class, $song->artists());
        $this->assertEquals($artist_count, $song->artists()->count());
        $this->assertInstanceOf(Artist::class, $song->artists()->first());
        $this->assertEquals(ArtistSong::class, $song->artists()->getPivotClass());
    }
}
