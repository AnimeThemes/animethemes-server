<?php

namespace Tests\Feature\Http;

use App\Enums\ImageFacet;
use App\Models\Image;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\WithoutEvents;
use Illuminate\Http\Testing\File;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Tests\TestCase;

class ImageTest extends TestCase
{
    use RefreshDatabase, WithFaker, WithoutEvents;

    /**
     * If the image is soft deleted, the user shall be redirected to the Welcome Screen.
     *
     * @return void
     */
    public function testSoftDeleteImageStreamingRedirect()
    {
        $image = Image::factory()->create();

        $image->delete();

        $response = $this->get(route('image.show', ['image' => $image]));

        $response->assertRedirect(Config::get('app.url'));
    }

    /**
     * The image show route shall stream the image.
     *
     * @return void
     */
    public function testImageStreaming()
    {
        $fs = Storage::fake('images');
        $file = File::fake()->image($this->faker->word());
        $fs_file = $fs->put('', $file);

        $image = Image::create([
            'path' => $fs_file,
            'facet' => ImageFacet::getRandomValue(),
        ]);

        $response = $this->get(route('image.show', ['image' => $image]));

        $this->assertInstanceOf(StreamedResponse::class, $response->baseResponse);
    }
}
