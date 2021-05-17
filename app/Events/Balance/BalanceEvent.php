<?php

namespace App\Events\Balance;

use App\Models\Balance;

abstract class BalanceEvent
{
    /**
     * The balance that has fired this event.
     *
     * @var \App\Models\Balance
     */
    protected $balance;

    /**
     * Create a new event instance.
     *
     * @param \App\Models\Balance $balance
     * @return void
     */
    public function __construct(Balance $balance)
    {
        $this->balance = $balance;
    }

    /**
     * Get the balance that has fired this event.
     *
     * @return \App\Models\Balance
     */
    public function getBalance()
    {
        return $this->balance;
    }
}
