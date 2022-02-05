<?php

declare(strict_types=1);

namespace Tests\Feature\Events\Wiki;

use App\Events\Wiki\ExternalResource\ExternalResourceCreated;
use App\Events\Wiki\ExternalResource\ExternalResourceDeleted;
use App\Events\Wiki\ExternalResource\ExternalResourceRestored;
use App\Events\Wiki\ExternalResource\ExternalResourceUpdated;
use App\Models\Wiki\ExternalResource;
use Illuminate\Support\Facades\Event;
use Tests\TestCase;

/**
 * Class ExternalResourceTest.
 */
class ExternalResourceTest extends TestCase
{
    /**
     * When a Resource is created, an ExternalResourceCreated event shall be dispatched.
     *
     * @return void
     */
    public function testExternalResourceCreatedEventDispatched(): void
    {
        Event::fake();

        ExternalResource::factory()->createOne();

        Event::assertDispatched(ExternalResourceCreated::class);
    }

    /**
     * When a Resource is deleted, an ExternalResourceDeleted event shall be dispatched.
     *
     * @return void
     */
    public function testExternalResourceDeletedEventDispatched(): void
    {
        Event::fake();

        $resource = ExternalResource::factory()->createOne();

        $resource->delete();

        Event::assertDispatched(ExternalResourceDeleted::class);
    }

    /**
     * When a Resource is restored, an ExternalResourceRestored event shall be dispatched.
     *
     * @return void
     */
    public function testExternalResourceRestoredEventDispatched(): void
    {
        Event::fake();

        $resource = ExternalResource::factory()->createOne();

        $resource->restore();

        Event::assertDispatched(ExternalResourceRestored::class);
    }

    /**
     * When a Resource is restored, an ExternalResourceUpdated event shall not be dispatched.
     * Note: This is a customization that overrides default framework behavior.
     * An updated event is fired on restore.
     *
     * @return void
     */
    public function testExternalResourceRestoresQuietly(): void
    {
        Event::fake();

        $resource = ExternalResource::factory()->createOne();

        $resource->restore();

        Event::assertNotDispatched(ExternalResourceUpdated::class);
    }

    /**
     * When an ExternalResource is updated, an ExternalResourceUpdated event shall be dispatched.
     *
     * @return void
     */
    public function testExternalResourceUpdatedEventDispatched(): void
    {
        Event::fake();

        $resource = ExternalResource::factory()->createOne();
        $changes = ExternalResource::factory()->makeOne();

        $resource->fill($changes->getAttributes());
        $resource->save();

        Event::assertDispatched(ExternalResourceUpdated::class);
    }
}
