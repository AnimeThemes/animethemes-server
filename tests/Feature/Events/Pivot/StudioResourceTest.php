<?php

declare(strict_types=1);

namespace Tests\Feature\Events\Pivot;

use App\Events\Pivot\StudioResource\StudioResourceCreated;
use App\Events\Pivot\StudioResource\StudioResourceDeleted;
use App\Events\Pivot\StudioResource\StudioResourceUpdated;
use App\Models\Wiki\ExternalResource;
use App\Models\Wiki\Studio;
use App\Pivots\StudioResource;
use Illuminate\Support\Facades\Event;
use Tests\TestCase;

/**
 * Class StudioResourceTest.
 */
class StudioResourceTest extends TestCase
{
    /**
     * When a Studio is attached to a Resource or vice versa, a StudioResourceCreated event shall be dispatched.
     *
     * @return void
     */
    public function testStudioResourceCreatedEventDispatched()
    {
        Event::fake();

        $studio = Studio::factory()->createOne();
        $resource = ExternalResource::factory()->createOne();

        $studio->resources()->attach($resource);

        Event::assertDispatched(StudioResourceCreated::class);
    }

    /**
     * When a Studio is detached to a Resource or vice versa, a StudioResourceDeleted event shall be dispatched.
     *
     * @return void
     */
    public function testStudioResourceDeletedEventDispatched()
    {
        Event::fake();

        $studio = Studio::factory()->createOne();
        $resource = ExternalResource::factory()->createOne();

        $studio->resources()->attach($resource);
        $studio->resources()->detach($resource);

        Event::assertDispatched(StudioResourceDeleted::class);
    }

    /**
     * When a Studio Resource pivot is updated, a StudioResourceUpdated event shall be dispatched.
     *
     * @return void
     */
    public function testStudioResourceUpdatedEventDispatched()
    {
        Event::fake();

        $studio = Studio::factory()->createOne();
        $resource = ExternalResource::factory()->createOne();

        $studioResource = StudioResource::factory()
            ->for($studio, 'studio')
            ->for($resource, 'resource')
            ->createOne();

        $changes = StudioResource::factory()
            ->for($studio, 'studio')
            ->for($resource, 'resource')
            ->makeOne();

        $studioResource->fill($changes->getAttributes());
        $studioResource->save();

        Event::assertDispatched(StudioResourceUpdated::class);
    }
}
