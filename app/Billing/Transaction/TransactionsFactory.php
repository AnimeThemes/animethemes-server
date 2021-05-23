<?php

namespace App\Billing\Transaction;

use App\Enums\Billing\Service;

class TransactionsFactory
{
    /**
     * Create a new collector instance from service.
     *
     * @param Service $service
     * @return \App\Contracts\Billing\ServiceTransactions|null
     */
    public static function create(Service $service)
    {
        switch ($service->value) {
        case Service::DIGITALOCEAN:
            return new DigitalOceanTransactions;
        }

        return null;
    }
}
