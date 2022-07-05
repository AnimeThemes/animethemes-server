<?php

declare(strict_types=1);

namespace Tests\Feature\Events\Billing;

use App\Events\Billing\Transaction\TransactionCreated;
use App\Events\Billing\Transaction\TransactionDeleted;
use App\Events\Billing\Transaction\TransactionRestored;
use App\Events\Billing\Transaction\TransactionUpdated;
use App\Models\Billing\Transaction;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Event;
use Tests\TestCase;

/**
 * Class TransactionTest.
 */
class TransactionTest extends TestCase
{
    /**
     * When a Transaction is created, an TransactionCreated event shall be dispatched.
     *
     * @return void
     */
    public function testTransactionCreatedEventDispatched(): void
    {
        Event::fake();

        Transaction::factory()->createOne();

        Event::assertDispatched(TransactionCreated::class);
    }

    /**
     * When a Transaction is deleted, an TransactionDeleted event shall be dispatched.
     *
     * @return void
     */
    public function testTransactionDeletedEventDispatched(): void
    {
        Event::fake();

        $transaction = Transaction::factory()->createOne();

        $transaction->delete();

        Event::assertDispatched(TransactionDeleted::class);
    }

    /**
     * When a Transaction is restored, an TransactionRestored event shall be dispatched.
     *
     * @return void
     */
    public function testTransactionRestoredEventDispatched(): void
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
    public function testTransactionRestoresQuietly(): void
    {
        Event::fake();

        $transaction = Transaction::factory()->createOne();

        $transaction->restore();

        Event::assertNotDispatched(TransactionUpdated::class);
    }

    /**
     * When a Transaction is updated, an TransactionUpdated event shall be dispatched.
     *
     * @return void
     */
    public function testTransactionUpdatedEventDispatched(): void
    {
        Event::fake();

        $transaction = Transaction::factory()->createOne();
        $changes = Transaction::factory()->makeOne();

        $transaction->fill($changes->getAttributes());
        $transaction->save();

        Event::assertDispatched(TransactionUpdated::class);
    }

    /**
     * The TransactionUpdated event shall contain embed fields.
     *
     * @return void
     */
    public function testTransactionUpdatedEventEmbedFields(): void
    {
        Event::fake();

        $transaction = Transaction::factory()->createOne();
        $changes = Transaction::factory()->makeOne();

        $transaction->fill($changes->getAttributes());
        $transaction->save();

        Event::assertDispatched(TransactionUpdated::class, function (TransactionUpdated $event) {
            $message = $event->getDiscordMessage();

            return ! empty(Arr::get($message->embed, 'fields'));
        });
    }
}
