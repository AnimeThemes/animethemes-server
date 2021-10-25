<?php

declare(strict_types=1);

namespace Tests\Feature\Events\Wiki;

use App\Events\Wiki\Studio\StudioCreated;
use App\Events\Wiki\Studio\StudioDeleted;
use App\Events\Wiki\Studio\StudioRestored;
use App\Events\Wiki\Studio\StudioUpdated;
use App\Models\Wiki\Studio;
use Illuminate\Support\Facades\Event;
use Tests\TestCase;

/**
 * Class StudioTest.
 */
class StudioTest extends TestCase
{
    /**
     * When a Studio is created, a StudioCreated event shall be dispatched.
     *
     * @return void
     */
    public function testStudioCreatedEventDispatched()
    {
        Event::fake();

        Studio::factory()->createOne();

        Event::assertDispatched(StudioCreated::class);
    }

    /**
     * When a Studio is deleted, a StudioDeleted event shall be dispatched.
     *
     * @return void
     */
    public function testStudioDeletedEventDispatched()
    {
        Event::fake();

        $studio = Studio::factory()->createOne();

        $studio->delete();

        Event::assertDispatched(StudioDeleted::class);
    }

    /**
     * When a Studio is restored, a StudioRestored event shall be dispatched.
     *
     * @return void
     */
    public function testStudioRestoredEventDispatched()
    {
        Event::fake();

        $studio = Studio::factory()->createOne();

        $studio->restore();

        Event::assertDispatched(StudioRestored::class);
    }

    /**
     * When a Studio is updated, a StudioUpdated event shall be dispatched.
     *
     * @return void
     */
    public function testStudioUpdatedEventDispatched()
    {
        Event::fake();

        $studio = Studio::factory()->createOne();
        $changes = Studio::factory()->makeOne();

        $studio->fill($changes->getAttributes());
        $studio->save();

        Event::assertDispatched(StudioUpdated::class);
    }
}
