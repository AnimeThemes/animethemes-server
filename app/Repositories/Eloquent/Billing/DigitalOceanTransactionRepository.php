<?php

declare(strict_types=1);

namespace App\Repositories\Eloquent\Billing;

use App\Enums\Models\Billing\Service;
use App\Models\Billing\Transaction;
use App\Repositories\Eloquent\EloquentRepository;
use Illuminate\Database\Eloquent\Builder;

/**
 * Class DigitalOceanTransactionRepository.
 *
 * @extends EloquentRepository<Transaction>
 */
class DigitalOceanTransactionRepository extends EloquentRepository
{
    /**
     * Get the underlying query builder.
     *
     * @return Builder
     */
    protected function builder(): Builder
    {
        return Transaction::query()->where(Transaction::ATTRIBUTE_SERVICE, Service::DIGITALOCEAN);
    }

    /**
     * Filter repository models.
     *
     * @param  string  $filter
     * @param  mixed  $value
     * @return void
     */
    public function handleFilter(string $filter, mixed $value = null): void
    {
        // not supported
    }
}
