<?php

namespace Tests\Feature\Api;

use App\Models\Artist;
use App\Models\ExternalResource;
use App\Models\Song;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class ArtistTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    /**
     *
     *
     * @return void
     */
    public function testShowArtistAttributes()
    {
        $artist = Artist::factory()->create();

        $response = $this->get(route('api.artist.show', ['artist' => $artist]));

        $response->assertJson(static::getData($artist));
    }

    public function testShowArtistSongsAttributes()
    {
        $artist = Artist::factory()
            ->has(Song::factory()->count($this->faker->randomDigitNotNull))
            ->create();

        $response = $this->get(route('api.artist.show', ['artist' => $artist]));

        $response->assertJson([
            'songs' => $artist->songs->map(function($song) {
                return SongTest::getData($song);
            })->toArray()
        ]);
    }

    public function testShowArtistMembersAttributes()
    {
        $artist = Artist::factory()
            ->has(Artist::factory()->count($this->faker->randomDigitNotNull), 'members')
            ->create();

        $response = $this->get(route('api.artist.show', ['artist' => $artist]));

        $response->assertJson([
            'members' => $artist->members->map(function($member) {
                return static::getData($member);
            })->toArray()
        ]);
    }

    public function testShowArtistGroupsAttributes()
    {
        $artist = Artist::factory()
            ->has(Artist::factory()->count($this->faker->randomDigitNotNull), 'groups')
            ->create();

        $response = $this->get(route('api.artist.show', ['artist' => $artist]));

        $response->assertJson([
            'groups' => $artist->groups->map(function($group) {
                return static::getData($group);
            })->toArray()
        ]);
    }

    public function testShowArtistResourcesAttributes() {
        $artist = Artist::factory()
            ->has(ExternalResource::factory()->count($this->faker->randomDigitNotNull))
            ->create();

        $response = $this->get(route('api.artist.show', ['artist' => $artist]));

        $response->assertJson([
            'resources' => $artist->externalResources->map(function($resource) {
                return ExternalResourceTest::getData($resource);
            })->toArray()
        ]);
    }

    public static function getData(Artist $artist) {
        return [
            'id' => $artist->artist_id,
            'name' => $artist->name,
            'alias' => $artist->alias,
            'created_at' => $artist->created_at->toJSON(),
            'updated_at' => $artist->updated_at->toJSON()
        ];
    }
}
