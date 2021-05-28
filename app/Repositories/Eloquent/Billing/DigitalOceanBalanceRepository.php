<?php

namespace App\Repositories\Eloquent\Billing;

use App\Enums\Billing\Service;
use App\Models\Billing\Balance;
use App\Repositories\Eloquent\EloquentRepository;
use Carbon\Carbon;

class DigitalOceanBalanceRepository extends EloquentRepository
{
    /**
     * Get all models from the repository.
     *
     * @return \Illuminate\Support\Collection
     */
    public function all()
    {
        $currentMonth = Carbon::now();

        return Balance::where('service', Service::DIGITALOCEAN)
            ->whereBetween('date', [$currentMonth->copy()->startOfMonth(), $currentMonth->copy()->endOfMonth()])
            ->get();
    }
}
