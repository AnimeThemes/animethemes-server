<?php

declare(strict_types=1);

namespace Tests\Feature\Events\Admin;

use App\Events\Admin\Dump\DumpCreated;
use App\Events\Admin\Dump\DumpDeleted;
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
    public function testDumpCreatedEventDispatched(): void
    {
        Dump::factory()->create();

        Event::assertDispatched(DumpCreated::class);
    }

    /**
     * When a Dump is deleted, a DumpDeleted event shall be dispatched.
     *
     * @return void
     */
    public function testDumpDeletedEventDispatched(): void
    {
        $dump = Dump::factory()->create();

        $dump->delete();

        Event::assertDispatched(DumpDeleted::class);
    }

    /**
     * When a Dump is updated, a DumpUpdated event shall be dispatched.
     *
     * @return void
     */
    public function testDumpUpdatedEventDispatched(): void
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
    public function testDumpUpdatedEventEmbedFields(): void
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
