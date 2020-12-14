<?php

namespace Tests\Feature\Http\Api;

use App\Models\Anime;
use App\Models\Artist;
use App\Models\Image;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class ImageTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    /**
     * The Image Index Endpoint shall display the Image attributes.
     *
     * @return void
     */
    public function testImageIndexAttributes()
    {
        $images = Image::factory()
            ->count($this->faker->randomDigitNotNull)
            ->create();

        $response = $this->get(route('api.image.index'));

        $response->assertJson([
            'images' => $images->map(function ($image) {
                return static::getData($image);
            })->toArray(),
        ]);
    }

    /**
     * The Show Anime Endpoint shall display the Anime attributes.
     *
     * @return void
     */
    public function testShowImageAttributes()
    {
        $image = Image::factory()->create();

        $response = $this->get(route('api.image.show', ['image' => $image]));

        $response->assertJson(static::getData($image));
    }

    /**
     * The Show Image Endpoint shall display the anime relation in an 'anime' attribute.
     *
     * @return void
     */
    public function testShowImageAnimeAttributes()
    {
        $image = Image::factory()
            ->has(Anime::factory()->count($this->faker->randomDigitNotNull))
            ->create();

        $response = $this->get(route('api.image.show', ['image' => $image]));

        $response->assertJson([
            'anime' => $image->anime->map(function ($anime) {
                return AnimeTest::getData($anime);
            })->toArray(),
        ]);
    }

    /**
     * The Show Image Endpoint shall display the artists relation in an 'artists' attribute.
     *
     * @return void
     */
    public function testShowImageArtistsAttributes()
    {
        $image = Image::factory()
            ->has(Artist::factory()->count($this->faker->randomDigitNotNull))
            ->create();

        $response = $this->get(route('api.image.show', ['image' => $image]));

        $response->assertJson([
            'artists' => $image->artists->map(function ($artist) {
                return ArtistTest::getData($artist);
            })->toArray(),
        ]);
    }

    /**
     * Get attributes for Image resource.
     *
     * @param \App\Models\Image $image
     * @return array
     */
    public static function getData(Image $image)
    {
        return [
            'id' => $image->image_id,
            'path' => $image->path,
            'facet' => strval(optional($image->facet)->description),
            'created_at' => $image->created_at->toJSON(),
            'updated_at' => $image->updated_at->toJSON(),
            'link' => Storage::disk('images')->url($image->path),
        ];
    }
}
