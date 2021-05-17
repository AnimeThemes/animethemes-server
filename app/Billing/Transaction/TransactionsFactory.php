<?php

namespace App\Billing\Transaction;

use App\Enums\BillingService;

class TransactionsFactory
{
    /**
     * Create a new collector instance from service.
     *
     * @param BillingService $service
     * @return \App\Contracts\Billing\ServiceTransactions|null
     */
    public static function create(BillingService $service)
    {
        switch ($service->value) {
        case BillingService::DIGITALOCEAN:
            return new DigitalOceanTransactions;
        }

        return null;
    }
}
