<?php

declare(strict_types=1);

namespace App\Repositories\Eloquent\Billing;

use App\Enums\Http\Api\Filter\ComparisonOperator;
use App\Enums\Models\Billing\Service;
use App\Models\Billing\Balance;
use App\Repositories\Eloquent\EloquentRepository;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Date;

/**
 * Class DigitalOceanBalanceRepository.
 *
 * @extends EloquentRepository<Balance>
 */
class DigitalOceanBalanceRepository extends EloquentRepository
{
    /**
     * Get the underlying query builder.
     *
     * @return Builder
     */
    protected function builder(): Builder
    {
        $now = Date::now();

        return Balance::query()
            ->where(Balance::ATTRIBUTE_SERVICE, Service::DIGITALOCEAN)
            ->whereMonth(Balance::ATTRIBUTE_DATE, ComparisonOperator::EQ->value, $now)
            ->whereYear(Balance::ATTRIBUTE_DATE, ComparisonOperator::EQ->value, $now);
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
