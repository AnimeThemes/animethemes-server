<?php declare(strict_types=1);

namespace Events\Billing;

use App\Events\Billing\Transaction\TransactionCreated;
use App\Events\Billing\Transaction\TransactionDeleted;
use App\Events\Billing\Transaction\TransactionRestored;
use App\Events\Billing\Transaction\TransactionUpdated;
use App\Models\Billing\Transaction;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Event;
use Tests\TestCase;

/**
 * Class TransactionTest
 * @package Events\Billing
 */
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
