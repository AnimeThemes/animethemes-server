<?php

declare(strict_types=1);

namespace App\Events\Billing\Balance;

use App\Events\Base\Admin\AdminDeletedEvent;
use App\Models\Billing\Balance;

/**
 * Class BalanceDeleted.
 *
 * @extends AdminDeletedEvent<Balance>
 */
class BalanceDeleted extends AdminDeletedEvent
{
    /**
     * Create a new event instance.
     *
     * @param  Balance  $balance
     */
    public function __construct(Balance $balance)
    {
        parent::__construct($balance);
    }

    /**
     * Get the model that has fired this event.
     *
     * @return Balance
     */
    public function getModel(): Balance
    {
        return $this->model;
    }

    /**
     * Get the description for the Discord message payload.
     *
     * @return string
     */
    protected function getDiscordMessageDescription(): string
    {
        return "Balance '**{$this->getModel()->getName()}**' has been deleted.";
    }
}
