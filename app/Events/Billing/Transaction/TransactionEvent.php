<?php

declare(strict_types=1);

namespace App\Events\Billing\Transaction;

use App\Models\Billing\Transaction;

/**
 * Class TransactionEvent.
 */
abstract class TransactionEvent
{
    /**
     * Create a new event instance.
     *
     * @param  Transaction  $transaction
     * @return void
     */
    public function __construct(protected Transaction $transaction)
    {
    }

    /**
     * Get the transaction that has fired this event.
     *
     * @return Transaction
     */
    public function getTransaction(): Transaction
    {
        return $this->transaction;
    }
}
