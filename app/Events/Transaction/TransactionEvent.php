<?php

namespace App\Events\Transaction;

use App\Models\Transaction;

abstract class TransactionEvent
{
    /**
     * The transaction that has fired this event.
     *
     * @var \App\Models\Transaction
     */
    protected $transaction;

    /**
     * Create a new event instance.
     *
     * @param \App\Models\Transaction $transaction
     * @return void
     */
    public function __construct(Transaction $transaction)
    {
        $this->transaction = $transaction;
    }

    /**
     * Get the transaction that has fired this event.
     *
     * @return \App\Models\Transaction
     */
    public function getTransaction()
    {
        return $this->transaction;
    }
}
