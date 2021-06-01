<?php

declare(strict_types=1);

namespace App\Repositories\Eloquent\Billing;

use App\Enums\Billing\Service;
use App\Models\Billing\Balance;
use App\Repositories\Eloquent\EloquentRepository;
use Carbon\Carbon;
use Illuminate\Support\Collection;

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
        $now = Carbon::now();

        return Balance::where('service', Service::DIGITALOCEAN)
            ->whereMonth('date', strval($now->month))
            ->whereYear('date', strval($now->year))
            ->get();
    }
}
