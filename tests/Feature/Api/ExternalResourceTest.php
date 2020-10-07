<?php

namespace Tests\Feature\Api;

use App\Models\Anime;
use App\Models\Artist;
use App\Models\ExternalResource;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class ExternalResourceTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    /**
     * The Resource Index Endpoint shall display the Resource attributes
     *
     * @return void
     */
    public function testResourceIndexAttributes()
    {
        $resources = ExternalResource::factory()
            ->count($this->faker->randomDigitNotNull)
            ->create();

        $response = $this->get(route('api.resource.index'));

        $response->assertJson([
            'resources' => $resources->map(function($resource) {
                return static::getData($resource);
            })->toArray()
        ]);
    }

    /**
     * The Show Resource Endpoint shall display the Resource attributes
     *
     * @return void
     */
    public function testShowResourceAttributes()
    {
        $resource = ExternalResource::factory()->create();

        $response = $this->get(route('api.resource.show', ['resource' => $resource]));

        $response->assertJson(static::getData($resource));
    }

    /**
     * The Show Resource Endpoint shall display the anime relation in an 'anime' attribute
     *
     * @return void
     */
    public function testShowResourceAnimeAttributes()
    {
        $resource = ExternalResource::factory()
            ->has(Anime::factory()->count($this->faker->randomDigitNotNull))
            ->create();

        $response = $this->get(route('api.resource.show', ['resource' => $resource]));

        $response->assertJson([
            'anime' => $resource->anime->map(function($anime) {
                return AnimeTest::getData($anime);
            })->toArray()
        ]);
    }

    /**
     * The Show Resource Endpoint shall display the artists relation in an 'artists' attribute
     *
     * @return void
     */
    public function testShowResourceArtistsAttributes()
    {
        $resource = ExternalResource::factory()
            ->has(Artist::factory()->count($this->faker->randomDigitNotNull))
            ->create();

        $response = $this->get(route('api.resource.show', ['resource' => $resource]));

        $response->assertJson([
            'artists' => $resource->artists->map(function($artist) {
                return ArtistTest::getData($artist);
            })->toArray()
        ]);
    }

    /**
     * Get attributes for Resource resource
     *
     * @param ExternalResource $resource
     * @return array
     */
    public static function getData(ExternalResource $resource) {
        return [
            'id' => $resource->resource_id,
            'link' => $resource->link,
            'external_id' => is_null($resource->external_id) ? '' : $resource->external_id,
            'type' => strval(optional($resource->type)->description),
        ];
    }
}
