<?php

namespace Tests\Feature\Events;

use App\Events\Transaction\TransactionCreated;
use App\Events\Transaction\TransactionDeleted;
use App\Events\Transaction\TransactionRestored;
use App\Events\Transaction\TransactionUpdated;
use App\Models\Transaction;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Event;
use Tests\TestCase;

class TransactionTest extends TestCase
{
    use RefreshDatabase;

    /**
     * When an Transaction is created, an TransactionCreated event shall be dispatched.
     *
     * @return void
     */
    public function testTransactionCreatedEventDispatched()
    {
        Event::fake();

        Transaction::factory()->create();

        Event::assertDispatched(TransactionCreated::class);
    }

    /**
     * When an Transaction is deleted, an TransactionDeleted event shall be dispatched.
     *
     * @return void
     */
    public function testTransactionDeletedEventDispatched()
    {
        Event::fake();

        $transaction = Transaction::factory()->create();

        $transaction->delete();

        Event::assertDispatched(TransactionDeleted::class);
    }

    /**
     * When an Transaction is restored, an TransactionRestored event shall be dispatched.
     *
     * @return void
     */
    public function testTransactionRestoredEventDispatched()
    {
        Event::fake();

        $transaction = Transaction::factory()->create();

        $transaction->restore();

        Event::assertDispatched(TransactionRestored::class);
    }

    /**
     * When an Transaction is updated, an TransactionUpdated event shall be dispatched.
     *
     * @return void
     */
    public function testTransactionUpdatedEventDispatched()
    {
        Event::fake();

        $transaction = Transaction::factory()->create();
        $changes = Transaction::factory()->make();

        $transaction->fill($changes->getAttributes());
        $transaction->save();

        Event::assertDispatched(TransactionUpdated::class);
    }
}
