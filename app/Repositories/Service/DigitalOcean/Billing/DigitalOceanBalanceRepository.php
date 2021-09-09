<?php

declare(strict_types=1);

namespace App\Repositories\Service\DigitalOcean\Billing;

use App\Contracts\Repositories\Repository;
use App\Enums\Http\Api\Filter\AllowedDateFormat;
use App\Enums\Models\Billing\BalanceFrequency;
use App\Enums\Models\Billing\Service;
use App\Models\Billing\Balance;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Client\RequestException;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Date;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

/**
 * Class DigitalOceanBalanceRepository.
 */
class DigitalOceanBalanceRepository implements Repository
{
    /**
     * Get all models from the repository.
     *
     * @param  array  $columns
     * @return Collection
     */
    public function all(array $columns = ['*']): Collection
    {
        // Do not proceed if we do not have authorization to the DO API
        $doBearerToken = Config::get('services.do.token');
        if ($doBearerToken === null) {
            Log::error('DO_BEARER_TOKEN must be configured in your env file.');

            return Collection::make();
        }

        try {
            $response = Http::withToken($doBearerToken)
                ->contentType('application/json')
                ->get('https://api.digitalocean.com/v2/customers/my/balance')
                ->throw()
                ->json();

            $balance = Balance::factory()->makeOne([
                'date' => Date::now()->firstOfMonth()->format(AllowedDateFormat::YMD),
                'service' => Service::DIGITALOCEAN,
                'frequency' => BalanceFrequency::MONTHLY,
                'usage' => Arr::get($response, 'month_to_date_usage'),
                'balance' => -1.0 * floatval(Arr::get($response, 'month_to_date_balance')),
            ]);

            return collect([$balance]);
        } catch (RequestException $e) {
            Log::info($e->getMessage());

            return Collection::make();
        }
    }

    /**
     * Save model to the repository.
     *
     * @param  Model  $model
     * @return bool
     */
    public function save(Model $model): bool
    {
        // Billing API is not writable
        return false;
    }

    /**
     * Delete model from the repository.
     *
     * @param  Model  $model
     * @return bool
     */
    public function delete(Model $model): bool
    {
        // Billing API is not writable
        return false;
    }

    /**
     * Update model in the repository.
     *
     * @param  Model  $model
     * @param  array  $attributes
     * @return bool
     */
    public function update(Model $model, array $attributes): bool
    {
        // Billing API is not writable
        return false;
    }
}
