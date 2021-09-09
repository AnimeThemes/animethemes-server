<?php

declare(strict_types=1);

namespace App\Events\Billing\Balance;

use App\Models\Billing\Balance;

/**
 * Class BalanceEvent.
 */
abstract class BalanceEvent
{
    /**
     * The balance that has fired this event.
     *
     * @var Balance
     */
    protected Balance $balance;

    /**
     * Create a new event instance.
     *
     * @param  Balance  $balance
     * @return void
     */
    public function __construct(Balance $balance)
    {
        $this->balance = $balance;
    }

    /**
     * Get the balance that has fired this event.
     *
     * @return Balance
     */
    public function getBalance(): Balance
    {
        return $this->balance;
    }
}
