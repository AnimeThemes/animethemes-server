<?php

declare(strict_types=1);

namespace Tests\Feature\Events\Wiki;

use App\Events\Wiki\ExternalResource\ExternalResourceCreated;
use App\Events\Wiki\ExternalResource\ExternalResourceDeleted;
use App\Events\Wiki\ExternalResource\ExternalResourceRestored;
use App\Events\Wiki\ExternalResource\ExternalResourceUpdated;
use App\Models\Wiki\ExternalResource;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Event;
use Tests\TestCase;

/**
 * Class ExternalResourceTest.
 */
class ExternalResourceTest extends TestCase
{
    use RefreshDatabase;

    /**
     * When a Resource is created, an ExternalResourceCreated event shall be dispatched.
     *
     * @return void
     */
    public function testExternalResourceCreatedEventDispatched()
    {
        Event::fake();

        ExternalResource::factory()->create();

        Event::assertDispatched(ExternalResourceCreated::class);
    }

    /**
     * When a Resource is deleted, an ExternalResourceDeleted event shall be dispatched.
     *
     * @return void
     */
    public function testExternalResourceDeletedEventDispatched()
    {
        Event::fake();

        $resource = ExternalResource::factory()->create();

        $resource->delete();

        Event::assertDispatched(ExternalResourceDeleted::class);
    }

    /**
     * When a Resource is restored, an ExternalResourceRestored event shall be dispatched.
     *
     * @return void
     */
    public function testExternalResourceRestoredEventDispatched()
    {
        Event::fake();

        $resource = ExternalResource::factory()->create();

        $resource->restore();

        Event::assertDispatched(ExternalResourceRestored::class);
    }

    /**
     * When an ExternalResource is updated, an ExternalResourceUpdated event shall be dispatched.
     *
     * @return void
     */
    public function testExternalResourceUpdatedEventDispatched()
    {
        Event::fake();

        $resource = ExternalResource::factory()->create();
        $changes = ExternalResource::factory()->make();

        $resource->fill($changes->getAttributes());
        $resource->save();

        Event::assertDispatched(ExternalResourceUpdated::class);
    }
}
