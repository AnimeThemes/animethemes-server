<?php

namespace Tests\Feature\Events;

use App\Events\Balance\BalanceCreated;
use App\Events\Balance\BalanceDeleted;
use App\Events\Balance\BalanceRestored;
use App\Events\Balance\BalanceUpdated;
use App\Models\Balance;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Event;
use Tests\TestCase;

class BalanceTest extends TestCase
{
    use RefreshDatabase;

    /**
     * When an Balance is created, an BalanceCreated event shall be dispatched.
     *
     * @return void
     */
    public function testBalanceCreatedEventDispatched()
    {
        Event::fake();

        Balance::factory()->create();

        Event::assertDispatched(BalanceCreated::class);
    }

    /**
     * When an Balance is deleted, an BalanceDeleted event shall be dispatched.
     *
     * @return void
     */
    public function testBalanceDeletedEventDispatched()
    {
        Event::fake();

        $balance = Balance::factory()->create();

        $balance->delete();

        Event::assertDispatched(BalanceDeleted::class);
    }

    /**
     * When an Balance is restored, an BalanceRestored event shall be dispatched.
     *
     * @return void
     */
    public function testBalanceRestoredEventDispatched()
    {
        Event::fake();

        $balance = Balance::factory()->create();

        $balance->restore();

        Event::assertDispatched(BalanceRestored::class);
    }

    /**
     * When an Balance is updated, an BalanceUpdated event shall be dispatched.
     *
     * @return void
     */
    public function testBalanceUpdatedEventDispatched()
    {
        Event::fake();

        $balance = Balance::factory()->create();
        $changes = Balance::factory()->make();

        $balance->fill($changes->getAttributes());
        $balance->save();

        Event::assertDispatched(BalanceUpdated::class);
    }
}
