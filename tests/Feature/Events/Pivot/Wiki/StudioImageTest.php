<?php

declare(strict_types=1);

namespace Tests\Feature\Events\Pivot\Wiki;

use App\Events\Pivot\Wiki\StudioImage\StudioImageCreated;
use App\Events\Pivot\Wiki\StudioImage\StudioImageDeleted;
use App\Models\Wiki\Image;
use App\Models\Wiki\Studio;
use Illuminate\Support\Facades\Event;
use Tests\TestCase;

/**
 * Class StudioImageTest.
 */
class StudioImageTest extends TestCase
{
    /**
     * When a Studio is attached to an Image or vice versa, a StudioImageCreated event shall be dispatched.
     *
     * @return void
     */
    public function testStudioImageCreatedEventDispatched(): void
    {
        Event::fake();

        $studio = Studio::factory()->createOne();
        $image = Image::factory()->createOne();

        $studio->images()->attach($image);

        Event::assertDispatched(StudioImageCreated::class);
    }

    /**
     * When a Studio is detached from an Image or vice versa, a StudioImageDeleted event shall be dispatched.
     *
     * @return void
     */
    public function testStudioImageDeletedEventDispatched(): void
    {
        Event::fake();

        $studio = Studio::factory()->createOne();
        $image = Image::factory()->createOne();

        $studio->images()->attach($image);
        $studio->images()->detach($image);

        Event::assertDispatched(StudioImageDeleted::class);
    }
}
