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
     * The Artist Index Endpoint shall display the Artist attributes
     *
     * @return void
     */
    public function testAnimeIndexAttributes()
    {
        $artists = Artist::factory()
            ->count($this->faker->randomDigitNotNull)
            ->create();

        $response = $this->get(route('api.artist.index'));

        $response->assertJson([
            'artists' => $artists->map(function($artist) {
                return static::getData($artist);
            })->toArray()
        ]);
    }

    /**
     * The Show Artist Endpoint shall display the Artist attributes
     *
     * @return void
     */
    public function testShowArtistAttributes()
    {
        $artist = Artist::factory()->create();

        $response = $this->get(route('api.artist.show', ['artist' => $artist]));

        $response->assertJson(static::getData($artist));
    }

    /**
     * The Show Artist Endpoint shall display the songs relation in a 'songs' attribute
     *
     * @return void
     */
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

    /**
     * The Show Artist Endpoint shall display the members relation in a 'members' attribute
     *
     * @return void
     */
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

    /**
     * The Show Artist Endpoint shall display the groups relation in a 'groups' attribute
     *
     * @return void
     */
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

    /**
     * The Show Artist Endpoint shall display the resources relation in a 'resources' attribute
     *
     * @return void
     */
    public function testShowArtistResourcesAttributes()
    {
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

    /**
     * Get attributes for Artist resource
     *
     * @param Artist $artist
     * @return array
     */
    public static function getData(Artist $artist)
    {
        return [
            'id' => $artist->artist_id,
            'name' => $artist->name,
            'alias' => $artist->alias,
            'created_at' => $artist->created_at->toJSON(),
            'updated_at' => $artist->updated_at->toJSON()
        ];
    }
}
