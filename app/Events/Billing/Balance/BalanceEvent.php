<?php

namespace App\Events\Billing\Balance;

use App\Models\Billing\Balance;

abstract class BalanceEvent
{
    /**
     * The balance that has fired this event.
     *
     * @var \App\Models\Billing\Balance
     */
    protected $balance;

    /**
     * Create a new event instance.
     *
     * @param \App\Models\Billing\Balance $balance
     * @return void
     */
    public function __construct(Balance $balance)
    {
        $this->balance = $balance;
    }

    /**
     * Get the balance that has fired this event.
     *
     * @return \App\Models\Billing\Balance
     */
    public function getBalance()
    {
        return $this->balance;
    }
}
