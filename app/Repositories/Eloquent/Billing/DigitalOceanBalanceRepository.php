<?php

declare(strict_types=1);

namespace App\Repositories\Eloquent\Billing;

use App\Enums\Http\Api\Filter\ComparisonOperator;
use App\Enums\Models\Billing\Service;
use App\Models\Billing\Balance;
use App\Repositories\Eloquent\EloquentRepository;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Date;

/**
 * Class DigitalOceanBalanceRepository.
 */
class DigitalOceanBalanceRepository extends EloquentRepository
{
    /**
     * Get all models from the repository.
     *
     * @param array $columns
     * @return Collection
     */
    public function all(array $columns = ['*']): Collection
    {
        $now = Date::now();

        return Balance::query()
            ->select($columns)
            ->where('service', Service::DIGITALOCEAN)
            ->whereMonth('date', ComparisonOperator::EQ, $now)
            ->whereYear('date', ComparisonOperator::EQ, $now)
            ->get();
    }
}
