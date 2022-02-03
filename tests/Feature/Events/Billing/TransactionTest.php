<?php

declare(strict_types=1);

namespace Tests\Feature\Events\Billing;

use App\Events\Billing\Transaction\TransactionCreated;
use App\Events\Billing\Transaction\TransactionDeleted;
use App\Events\Billing\Transaction\TransactionRestored;
use App\Events\Billing\Transaction\TransactionUpdated;
use App\Models\Billing\Transaction;
use Illuminate\Support\Facades\Event;
use Tests\TestCase;

/**
 * Class TransactionTest.
 */
class TransactionTest extends TestCase
{
    /**
     * When an Transaction is created, an TransactionCreated event shall be dispatched.
     *
     * @return void
     */
    public function testTransactionCreatedEventDispatched()
    {
        Event::fake();

        Transaction::factory()->createOne();

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

        $transaction = Transaction::factory()->createOne();

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

        $transaction = Transaction::factory()->createOne();

        $transaction->restore();

        Event::assertDispatched(TransactionRestored::class);
    }

    /**
     * When a Transaction is restored, a TransactionUpdated event shall not be dispatched.
     * Note: This is a customization that overrides default framework behavior.
     * An updated event is fired on restore.
     *
     * @return void
     */
    public function testTransactionRestoresQuietly()
    {
        Event::fake();

        $transaction = Transaction::factory()->createOne();

        $transaction->restore();

        Event::assertNotDispatched(TransactionUpdated::class);
    }

    /**
     * When an Transaction is updated, an TransactionUpdated event shall be dispatched.
     *
     * @return void
     */
    public function testTransactionUpdatedEventDispatched()
    {
        Event::fake();

        $transaction = Transaction::factory()->createOne();
        $changes = Transaction::factory()->makeOne();

        $transaction->fill($changes->getAttributes());
        $transaction->save();

        Event::assertDispatched(TransactionUpdated::class);
    }
}
