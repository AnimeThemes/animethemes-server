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

    public function testShowSongAttributes()
    {
        $song = Song::factory()->create();

        $response = $this->get(route('api.song.show', ['song' => $song]));

        $response->assertJson(static::getData($song));
    }

    public function testShowSongThemesAttributes() {
        $song = Song::factory()
            ->has(Theme::factory()->for(Anime::factory())->count($this->faker->randomDigitNotNull))
            ->create();

        $response = $this->get(route('api.song.show', ['song' => $song]));

        $response->assertJson([
            'themes' => $song->themes->map(function($theme) {
                return ThemeTest::getData($theme);
            })->toArray()
        ]);
    }

    public function testShowSongArtistsAttributes()
    {
        $song = Song::factory()
            ->has(Artist::factory()->count($this->faker->randomDigitNotNull))
            ->create();

        $response = $this->get(route('api.song.show', ['song' => $song]));

        $response->assertJson([
            'artists' => $song->artists->map(function($artist) {
                return ArtistTest::getData($artist);
            })->toArray()
        ]);
    }

    public static function getData(Song $song) {
        return [
            'id' => $song->song_id,
            'title' => strval($song->title),
            'created_at' => $song->created_at->toJSON(),
            'updated_at' => $song->updated_at->toJSON()
        ];
    }
}
