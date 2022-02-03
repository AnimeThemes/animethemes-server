<?php

declare(strict_types=1);

namespace Tests\Feature\Events\Wiki;

use App\Events\Wiki\Image\ImageCreated;
use App\Events\Wiki\Image\ImageDeleted;
use App\Events\Wiki\Image\ImageRestored;
use App\Events\Wiki\Image\ImageUpdated;
use App\Models\Wiki\Image;
use Illuminate\Support\Facades\Event;
use Tests\TestCase;

/**
 * Class ImageTest.
 */
class ImageTest extends TestCase
{
    /**
     * When an Image is created, an ImageCreated event shall be dispatched.
     *
     * @return void
     */
    public function testImageCreatedEventDispatched()
    {
        Event::fake();

        Image::factory()->createOne();

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

        $image = Image::factory()->createOne();

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

        $image = Image::factory()->createOne();

        $image->restore();

        Event::assertDispatched(ImageRestored::class);
    }

    /**
     * When an Image is restored, an ImageUpdated event shall not be dispatched.
     * Note: This is a customization that overrides default framework behavior.
     * An updated event is fired on restore.
     *
     * @return void
     */
    public function testImageRestoresQuietly()
    {
        Event::fake();

        $image = Image::factory()->createOne();

        $image->restore();

        Event::assertNotDispatched(ImageUpdated::class);
    }

    /**
     * When an Image is updated, an ImageUpdated event shall be dispatched.
     *
     * @return void
     */
    public function testImageUpdatedEventDispatched()
    {
        Event::fake();

        $image = Image::factory()->createOne();
        $changes = Image::factory()->makeOne();

        $image->fill($changes->getAttributes());
        $image->save();

        Event::assertDispatched(ImageUpdated::class);
    }
}
