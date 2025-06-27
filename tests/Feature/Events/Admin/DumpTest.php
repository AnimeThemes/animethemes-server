<?php

declare(strict_types=1);

namespace Tests\Feature\Events\Admin;

use App\Events\Admin\Dump\DumpCreated;
use App\Events\Admin\Dump\DumpDeleted;
use App\Events\Admin\Dump\DumpRestored;
use App\Events\Admin\Dump\DumpUpdated;
use App\Models\Admin\Dump;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Event;
use Tests\TestCase;

/**
 * Class DumpTest.
 */
class DumpTest extends TestCase
{
    /**
     * When a Dump is created, a DumpCreated event shall be dispatched.
     *
     * @return void
     */
    public function test_dump_created_event_dispatched(): void
    {
        Dump::factory()->create();

        Event::assertDispatched(DumpCreated::class);
    }

    /**
     * When a Dump is deleted, a DumpDeleted event shall be dispatched.
     *
     * @return void
     */
    public function test_dump_deleted_event_dispatched(): void
    {
        $dump = Dump::factory()->create();

        $dump->delete();

        Event::assertDispatched(DumpDeleted::class);
    }

    /**
     * When a Dump is restored, a DumpRestored event shall be dispatched.
     *
     * @return void
     */
    public function test_dump_restored_event_dispatched(): void
    {
        $dump = Dump::factory()->createOne();

        $dump->restore();

        Event::assertDispatched(DumpRestored::class);
    }

    /**
     * When a Dump is restored, a DumpUpdated event shall not be dispatched.
     * Note: This is a customization that overrides default framework behavior.
     * An updated event is fired on restore.
     *
     * @return void
     */
    public function test_dump_restores_quietly(): void
    {
        $dump = Dump::factory()->createOne();

        $dump->restore();

        Event::assertNotDispatched(DumpUpdated::class);
    }

    /**
     * When a Dump is updated, a DumpUpdated event shall be dispatched.
     *
     * @return void
     */
    public function test_dump_updated_event_dispatched(): void
    {
        $dump = Dump::factory()->createOne();
        $changes = Dump::factory()->makeOne();

        $dump->fill($changes->getAttributes());
        $dump->save();

        Event::assertDispatched(DumpUpdated::class);
    }

    /**
     * The DumpUpdated event shall contain embed fields.
     *
     * @return void
     */
    public function test_dump_updated_event_embed_fields(): void
    {
        $dump = Dump::factory()->createOne();
        $changes = Dump::factory()->makeOne();

        $dump->fill($changes->getAttributes());
        $dump->save();

        Event::assertDispatched(DumpUpdated::class, function (DumpUpdated $event) {
            $message = $event->getDiscordMessage();

            return ! empty(Arr::get($message->embed, 'fields'));
        });
    }
}
