<?php

namespace Tests\Feature\Http;

use App\Models\Image;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithoutEvents;
use Illuminate\Support\Facades\Config;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Tests\TestCase;

class ImageTest extends TestCase
{
    use RefreshDatabase, WithoutEvents;

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
        $image = Image::factory()->create();

        $response = $this->get(route('image.show', ['image' => $image]));

        $this->assertInstanceOf(StreamedResponse::class, $response->baseResponse);
    }
}
