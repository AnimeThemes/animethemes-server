<?php

declare(strict_types=1);

namespace App\Repositories\DigitalOcean\Billing;

use App\Enums\Http\Api\Filter\AllowedDateFormat;
use App\Enums\Models\Billing\BalanceFrequency;
use App\Enums\Models\Billing\Service;
use App\Models\Billing\Balance;
use App\Repositories\DigitalOcean\DigitalOceanRepository;
use Illuminate\Http\Client\RequestException;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Date;
use Illuminate\Support\Facades\Http;

/**
 * Class DigitalOceanBalanceRepository.
 *
 * @extends DigitalOceanRepository<Balance>
 */
class DigitalOceanBalanceRepository extends DigitalOceanRepository
{
    /**
     * Get models from the repository.
     *
     * @param  array  $columns
     * @return Collection<int, Balance>
     *
     * @throws RequestException
     */
    public function get(array $columns = ['*']): Collection
    {
        $response = Http::withToken(Config::get('services.do.token'))
            ->contentType('application/json')
            ->get('https://api.digitalocean.com/v2/customers/my/balance')
            ->throw()
            ->json();

        $balance = new Balance([
            Balance::ATTRIBUTE_BALANCE => -1.0 * floatval(Arr::get($response, 'month_to_date_balance')),
            Balance::ATTRIBUTE_DATE => Date::now()->firstOfMonth()->format(AllowedDateFormat::YMD),
            Balance::ATTRIBUTE_FREQUENCY => BalanceFrequency::MONTHLY,
            Balance::ATTRIBUTE_SERVICE => Service::DIGITALOCEAN,
            Balance::ATTRIBUTE_USAGE => Arr::get($response, 'month_to_date_usage'),
        ]);

        return collect([$balance]);
    }
}
