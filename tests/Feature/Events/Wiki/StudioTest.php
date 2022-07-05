<?php

declare(strict_types=1);

namespace Tests\Feature\Events\Wiki;

use App\Events\Wiki\Studio\StudioCreated;
use App\Events\Wiki\Studio\StudioDeleted;
use App\Events\Wiki\Studio\StudioRestored;
use App\Events\Wiki\Studio\StudioUpdated;
use App\Models\Wiki\Studio;
use Illuminate\Support\Arr;
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
    public function testStudioCreatedEventDispatched(): void
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
    public function testStudioDeletedEventDispatched(): void
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
    public function testStudioRestoredEventDispatched(): void
    {
        Event::fake();

        $studio = Studio::factory()->createOne();

        $studio->restore();

        Event::assertDispatched(StudioRestored::class);
    }

    /**
     * When a Studio is restored, a StudioUpdated event shall not be dispatched.
     * Note: This is a customization that overrides default framework behavior.
     * An updated event is fired on restore.
     *
     * @return void
     */
    public function testStudioRestoresQuietly(): void
    {
        Event::fake();

        $studio = Studio::factory()->createOne();

        $studio->restore();

        Event::assertNotDispatched(StudioUpdated::class);
    }

    /**
     * When a Studio is updated, a StudioUpdated event shall be dispatched.
     *
     * @return void
     */
    public function testStudioUpdatedEventDispatched(): void
    {
        Event::fake();

        $studio = Studio::factory()->createOne();
        $changes = Studio::factory()->makeOne();

        $studio->fill($changes->getAttributes());
        $studio->save();

        Event::assertDispatched(StudioUpdated::class);
    }

    /**
     * The StudioUpdated event shall contain embed fields.
     *
     * @return void
     */
    public function testStudioUpdatedEventEmbedFields(): void
    {
        Event::fake();

        $anime = Studio::factory()->createOne();
        $changes = Studio::factory()->makeOne();

        $anime->fill($changes->getAttributes());
        $anime->save();

        Event::assertDispatched(StudioUpdated::class, function (StudioUpdated $event) {
            $message = $event->getDiscordMessage();

            return ! empty(Arr::get($message->embed, 'fields'));
        });
    }
}
