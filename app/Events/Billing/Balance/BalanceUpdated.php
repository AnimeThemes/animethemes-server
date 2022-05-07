<?php

declare(strict_types=1);

namespace App\Events\Billing\Balance;

use App\Events\Base\Admin\AdminUpdatedEvent;
use App\Models\Billing\Balance;

/**
 * Class BalanceUpdated.
 *
 * @extends AdminUpdatedEvent<Balance>
 */
class BalanceUpdated extends AdminUpdatedEvent
{
    /**
     * Create a new event instance.
     *
     * @param  Balance  $balance
     */
    public function __construct(Balance $balance)
    {
        parent::__construct($balance);
        $this->initializeEmbedFields($balance);
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
        return "Balance '**{$this->getModel()->getName()}**' has been updated.";
    }
}
