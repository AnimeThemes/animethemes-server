<?php

declare(strict_types=1);

namespace Http;

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

/**
 * Class ImageTest
 * @package Http
 */
class ImageTest extends TestCase
{
    use RefreshDatabase;
    use WithFaker;
    use WithoutEvents;

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

        $response->assertRedirect(route('welcome'));
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
        $fsFile = $fs->putFile('', $file);
        $fsPathinfo = pathinfo(strval($fsFile));

        $image = Image::create([
            'path' => $fsFile,
            'facet' => ImageFacet::getRandomValue(),
            'size' => $this->faker->randomNumber(),
            'mimetype' => MimeType::fromFilename($fsPathinfo['basename']),
        ]);

        $response = $this->get(route('image.show', ['image' => $image]));

        static::assertInstanceOf(StreamedResponse::class, $response->baseResponse);
    }
}
