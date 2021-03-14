<?php

namespace Tests\Feature\Events;

use App\Events\Image\ImageCreated;
use App\Events\Image\ImageDeleted;
use App\Events\Image\ImageRestored;
use App\Events\Image\ImageUpdated;
use App\Models\Image;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Event;
use Tests\TestCase;

class ImageTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    /**
     * When an Image is created, an ImageCreated event shall be dispatched.
     *
     * @return void
     */
    public function testImageCreatedEventDispatched()
    {
        Event::fake();

        Image::factory()->create();

        Event::assertDispatched(ImageCreated::class);
    }

    /**
     * When an Image is deleted, an ImageDeleted event shall be dispatched.
     *
     * @return void
     */
    public function testImageDeletedEventDispatched()
    {
        Event::fake();

        $image = Image::factory()->create();

        $image->delete();

        Event::assertDispatched(ImageDeleted::class);
    }

    /**
     * When an Image is restored, an ImageRestored event shall be dispatched.
     *
     * @return void
     */
    public function testImageRestoredEventDispatched()
    {
        Event::fake();

        $image = Image::factory()->create();

        $image->restore();

        Event::assertDispatched(ImageRestored::class);
    }

    /**
     * When an Image is updated, an ImageUpdated event shall be dispatched.
     *
     * @return void
     */
    public function testImageUpdatedEventDispatched()
    {
        Event::fake();

        $image = Image::factory()->create();
        $changes = Image::factory()->make();

        $image->fill($changes->getAttributes());
        $image->save();

        Event::assertDispatched(ImageUpdated::class);
    }
}
