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
     * The transaction that has fired this event.
     *
     * @var Transaction
     */
    protected Transaction $transaction;

    /**
     * Create a new event instance.
     *
     * @param  Transaction  $transaction
     * @return void
     */
    public function __construct(Transaction $transaction)
    {
        $this->transaction = $transaction;
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
