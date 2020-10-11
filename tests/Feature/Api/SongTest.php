<?php

namespace Tests\Feature\Api;

use App\Models\Anime;
use App\Models\Artist;
use App\Models\Song;
use App\Models\Theme;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class SongTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    /**
     * The Song Index Endpoint shall display the Song attributes.
     *
     * @return void
     */
    public function testSongIndexAttributes()
    {
        $songs = Song::factory()
            ->count($this->faker->randomDigitNotNull)
            ->create();

        $response = $this->get(route('api.song.index'));

        $response->assertJson([
            'songs' => $songs->map(function ($song) {
                return static::getData($song);
            })->toArray(),
        ]);
    }

    /**
     * The Show Song Endpoint shall display the Song attributes.
     *
     * @return void
     */
    public function testShowSongAttributes()
    {
        $song = Song::factory()->create();

        $response = $this->get(route('api.song.show', ['song' => $song]));

        $response->assertJson(static::getData($song));
    }

    /**
     * The Show Song Endpoint shall display the themes relation in an 'themes' attribute.
     *
     * @return void
     */
    public function testShowSongThemesAttributes()
    {
        $song = Song::factory()
            ->has(Theme::factory()->for(Anime::factory())->count($this->faker->randomDigitNotNull))
            ->create();

        $response = $this->get(route('api.song.show', ['song' => $song]));

        $response->assertJson([
            'themes' => $song->themes->map(function ($theme) {
                return ThemeTest::getData($theme);
            })->toArray(),
        ]);
    }

    /**
     * The Show Song Endpoint shall display the artists relation in an 'artists' attribute.
     *
     * @return void
     */
    public function testShowSongArtistsAttributes()
    {
        $song = Song::factory()
            ->has(Artist::factory()->count($this->faker->randomDigitNotNull))
            ->create();

        $response = $this->get(route('api.song.show', ['song' => $song]));

        $response->assertJson([
            'artists' => $song->artists->map(function ($artist) {
                return ArtistTest::getData($artist);
            })->toArray(),
        ]);
    }

    /**
     * Get attributes for Song resource.
     *
     * @param Song $song
     * @return array
     */
    public static function getData(Song $song)
    {
        return [
            'id' => $song->song_id,
            'title' => strval($song->title),
            'created_at' => $song->created_at->toJSON(),
            'updated_at' => $song->updated_at->toJSON(),
        ];
    }
}
