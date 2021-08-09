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
     * @return Collection
     */
    public function all(): Collection
    {
        $now = Date::now();

        return Balance::query()
            ->where('service', Service::DIGITALOCEAN)
            ->whereMonth('date', ComparisonOperator::EQ, $now)
            ->whereYear('date', ComparisonOperator::EQ, $now)
            ->get();
    }
}
