<?php

namespace App\Repositories\Eloquent\Billing;

use App\Enums\Billing\Service;
use App\Models\Billing\Transaction;
use App\Repositories\Eloquent\EloquentRepository;

class DigitalOceanTransactionRepository extends EloquentRepository
{
    /**
     * Get all models from the repository.
     *
     * @return \Illuminate\Support\Collection
     */
    public function all()
    {
        return Transaction::where('service', Service::DIGITALOCEAN)->get();
    }
}
