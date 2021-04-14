<?php

namespace Tests\Feature\Http;

use App\Enums\ImageFacet;
use App\Models\Image;
use GuzzleHttp\Psr7\MimeType;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\WithoutEvents;
use Illuminate\Http\Testing\File;
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

        $response->assertRedirect(route('welcome.index'));
    }

    /**
     * The image show route shall stream the image.
     *
     * @return void
     */
    public function testImageStreaming()
    {
        $fs = Storage::fake('images');
        $file = File::fake()->image($this->faker->word().'.jpg');
        $fs_file = $fs->put('', $file);
        $fs_pathinfo = pathinfo(strval($fs_file));

        $image = Image::create([
            'path' => $fs_file,
            'facet' => ImageFacet::getRandomValue(),
            'size' => $this->faker->randomNumber(),
            'mimetype' => MimeType::fromFilename($fs_pathinfo['basename']),
        ]);

        $response = $this->get(route('image.show', ['image' => $image]));

        $this->assertInstanceOf(StreamedResponse::class, $response->baseResponse);
    }
}
