<?php

declare(strict_types=1);

namespace App\Repositories\Service\DigitalOcean\Billing;

use App\Contracts\Repositories\Repository;
use App\Enums\Http\Api\Filter\AllowedDateFormat;
use App\Enums\Models\Billing\Service;
use App\Models\Billing\Transaction;
use DateTime;
use DateTimeInterface;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Client\RequestException;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Http;
use RuntimeException;

/**
 * Class DigitalOceanTransactionRepository.
 */
class DigitalOceanTransactionRepository implements Repository
{
    /**
     * Get models from the repository.
     *
     * @param  array  $columns
     * @return Collection
     *
     * @throws RequestException
     */
    public function get(array $columns = ['*']): Collection
    {
        // Do not proceed if we do not have authorization to the DO API
        $doBearerToken = Config::get('services.do.token');
        if ($doBearerToken === null) {
            throw new RuntimeException('DO_BEARER_TOKEN must be configured in your env file.');
        }

        $sourceTransactions = [];

        $request = Http::withToken($doBearerToken)->contentType('application/json');

        $nextBillingHistory = 'https://api.digitalocean.com/v2/customers/my/billing_history?per_page=200';
        while (! empty($nextBillingHistory)) {
            // Try not to upset DO
            sleep(rand(2, 5));

            $response = $request->get($nextBillingHistory)->throw()->json();

            $billingHistory = Arr::get($response, 'billing_history', []);
            foreach ($billingHistory as $sourceTransaction) {
                $date = DateTime::createFromFormat(
                    '!'.DateTimeInterface::RFC3339,
                    Arr::get($sourceTransaction, 'date')
                );

                $sourceTransactions[] = Transaction::factory()->makeOne([
                    Transaction::ATTRIBUTE_AMOUNT => Arr::get($sourceTransaction, 'amount'),
                    Transaction::ATTRIBUTE_DATE => $date->format(AllowedDateFormat::YMD),
                    Transaction::ATTRIBUTE_DESCRIPTION => Arr::get($sourceTransaction, 'description'),
                    Transaction::ATTRIBUTE_EXTERNAL_ID => Arr::get($sourceTransaction, 'invoice_id'),
                    Transaction::ATTRIBUTE_SERVICE => Service::DIGITALOCEAN,
                ]);
            }

            $nextBillingHistory = Arr::get($response, 'links.pages.next');
        }

        return Collection::make($sourceTransactions);
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

    /**
     * Validate repository filter.
     *
     * @param  string  $filter
     * @param  mixed  $value
     * @return bool
     */
    public function validateFilter(string $filter, mixed $value = null): bool
    {
        // not supported
        return false;
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
