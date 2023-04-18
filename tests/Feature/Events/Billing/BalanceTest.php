<?php

declare(strict_types=1);

namespace Tests\Feature\Events\Billing;

use App\Events\Billing\Balance\BalanceCreated;
use App\Events\Billing\Balance\BalanceDeleted;
use App\Events\Billing\Balance\BalanceRestored;
use App\Events\Billing\Balance\BalanceUpdated;
use App\Models\Billing\Balance;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Event;
use Tests\TestCase;

/**
 * Class BalanceTest.
 */
class BalanceTest extends TestCase
{
    /**
     * When a Balance is created, an BalanceCreated event shall be dispatched.
     *
     * @return void
     */
    public function testBalanceCreatedEventDispatched(): void
    {
        Balance::factory()->createOne();

        Event::assertDispatched(BalanceCreated::class);
    }

    /**
     * When a Balance is deleted, an BalanceDeleted event shall be dispatched.
     *
     * @return void
     */
    public function testBalanceDeletedEventDispatched(): void
    {
        $balance = Balance::factory()->createOne();

        $balance->delete();

        Event::assertDispatched(BalanceDeleted::class);
    }

    /**
     * When a Balance is restored, an BalanceRestored event shall be dispatched.
     *
     * @return void
     */
    public function testBalanceRestoredEventDispatched(): void
    {
        $balance = Balance::factory()->createOne();

        $balance->restore();

        Event::assertDispatched(BalanceRestored::class);
    }

    /**
     * When a Balance is restored, a BalanceUpdated event shall not be dispatched.
     * Note: This is a customization that overrides default framework behavior.
     * An updated event is fired on restore.
     *
     * @return void
     */
    public function testBalanceRestoresQuietly(): void
    {
        $balance = Balance::factory()->createOne();

        $balance->restore();

        Event::assertNotDispatched(BalanceUpdated::class);
    }

    /**
     * When a Balance is updated, an BalanceUpdated event shall be dispatched.
     *
     * @return void
     */
    public function testBalanceUpdatedEventDispatched(): void
    {
        $balance = Balance::factory()->createOne();
        $changes = Balance::factory()->makeOne();

        $balance->fill($changes->getAttributes());
        $balance->save();

        Event::assertDispatched(BalanceUpdated::class);
    }

    /**
     * The BalanceUpdated event shall contain embed fields.
     *
     * @return void
     */
    public function testBalanceUpdatedEventEmbedFields(): void
    {
        $balance = Balance::factory()->createOne();
        $changes = Balance::factory()->makeOne();

        $balance->fill($changes->getAttributes());
        $balance->save();

        Event::assertDispatched(BalanceUpdated::class, function (BalanceUpdated $event) {
            $message = $event->getDiscordMessage();

            return ! empty(Arr::get($message->embed, 'fields'));
        });
    }
}
