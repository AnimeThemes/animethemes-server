<?php

declare(strict_types=1);

namespace Tests\Feature\Events\Pivot\Wiki;

use App\Events\Pivot\Wiki\StudioResource\StudioResourceCreated;
use App\Events\Pivot\Wiki\StudioResource\StudioResourceDeleted;
use App\Events\Pivot\Wiki\StudioResource\StudioResourceUpdated;
use App\Models\Wiki\ExternalResource;
use App\Models\Wiki\Studio;
use App\Pivots\Wiki\StudioResource;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Event;
use Tests\TestCase;

class StudioResourceTest extends TestCase
{
    /**
     * When a Studio is attached to a Resource or vice versa, a StudioResourceCreated event shall be dispatched.
     */
    public function testStudioResourceCreatedEventDispatched(): void
    {
        $studio = Studio::factory()->createOne();
        $resource = ExternalResource::factory()->createOne();

        $studio->resources()->attach($resource);

        Event::assertDispatched(StudioResourceCreated::class);
    }

    /**
     * When a Studio is detached to a Resource or vice versa, a StudioResourceDeleted event shall be dispatched.
     */
    public function testStudioResourceDeletedEventDispatched(): void
    {
        $studio = Studio::factory()->createOne();
        $resource = ExternalResource::factory()->createOne();

        $studio->resources()->attach($resource);
        $studio->resources()->detach($resource);

        Event::assertDispatched(StudioResourceDeleted::class);
    }

    /**
     * When a Studio Resource pivot is updated, a StudioResourceUpdated event shall be dispatched.
     */
    public function testStudioResourceUpdatedEventDispatched(): void
    {
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

    /**
     * The StudioResourceUpdated event shall contain embed fields.
     */
    public function testStudioResourceUpdatedEventEmbedFields(): void
    {
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

        Event::assertDispatched(StudioResourceUpdated::class, function (StudioResourceUpdated $event) {
            $message = $event->getDiscordMessage();

            return ! empty(Arr::get($message->embed, 'fields'));
        });
    }
}
