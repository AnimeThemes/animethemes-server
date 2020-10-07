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

    public function testShowResourceAttributes()
    {
        $resource = ExternalResource::factory()->create();

        $response = $this->get(route('api.resource.show', ['resource' => $resource]));

        $response->assertJson(static::getData($resource));
    }

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

    public static function getData(ExternalResource $resource) {
        return [
            'id' => $resource->resource_id,
            'link' => $resource->link,
            'external_id' => is_null($resource->external_id) ? '' : $resource->external_id,
            'type' => strval(optional($resource->type)->description),
        ];
    }
}
