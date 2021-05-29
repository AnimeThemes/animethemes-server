<?php

namespace App\Events\Billing\Transaction;

use App\Models\Billing\Transaction;

abstract class TransactionEvent
{
    /**
     * The transaction that has fired this event.
     *
     * @var \App\Models\Billing\Transaction
     */
    protected $transaction;

    /**
     * Create a new event instance.
     *
     * @param \App\Models\Billing\Transaction $transaction
     * @return void
     */
    public function __construct(Transaction $transaction)
    {
        $this->transaction = $transaction;
    }

    /**
     * Get the transaction that has fired this event.
     *
     * @return \App\Models\Billing\Transaction
     */
    public function getTransaction()
    {
        return $this->transaction;
    }
}
