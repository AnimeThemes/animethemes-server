<?php

declare(strict_types=1);

namespace App\Repositories\Eloquent\Billing;

use App\Enums\Models\Billing\Service;
use App\Models\Billing\Transaction;
use App\Repositories\Eloquent\EloquentRepository;
use Illuminate\Support\Collection;

/**
 * Class DigitalOceanTransactionRepository.
 */
class DigitalOceanTransactionRepository extends EloquentRepository
{
    /**
     * Get all models from the repository.
     *
     * @param array $columns
     * @return Collection
     */
    public function all(array $columns = ['*']): Collection
    {
        return Transaction::query()
            ->select($columns)
            ->where('service', Service::DIGITALOCEAN)
            ->get();
    }
}
