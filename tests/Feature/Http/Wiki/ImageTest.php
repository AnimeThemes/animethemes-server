<?php

declare(strict_types=1);

namespace Tests\Feature\Http\Wiki;

use App\Models\Wiki\Image;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\WithoutEvents;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Tests\TestCase;

/**
 * Class ImageTest.
 */
class ImageTest extends TestCase
{
    use WithFaker;
    use WithoutEvents;

    /**
     * If the image is soft-deleted, the user shall receive a forbidden exception.
     *
     * @return void
     */
    public function testSoftDeleteImageStreamingForbidden(): void
    {
        $image = Image::factory()->createOne();

        $image->delete();

        $response = $this->get(route('image.show', ['image' => $image]));

        $response->assertForbidden();
    }

    /**
     * The image show route shall stream the image.
     *
     * @return void
     */
    public function testImageStreaming(): void
    {
        $image = Image::factory()->createOne();

        $response = $this->get(route('image.show', ['image' => $image]));

        static::assertInstanceOf(StreamedResponse::class, $response->baseResponse);
    }
}
