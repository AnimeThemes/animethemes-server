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
use Illuminate\Support\Facades\Log;

/**
 * Class DigitalOceanTransactionRepository.
 */
class DigitalOceanTransactionRepository implements Repository
{
    /**
     * Get all models from the repository.
     *
     * @param array $columns
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

        $sourceTransactions = [];

        try {
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
                        'date' => $date->format(AllowedDateFormat::YMD),
                        'service' => Service::DIGITALOCEAN,
                        'description' => Arr::get($sourceTransaction, 'description'),
                        'amount' => Arr::get($sourceTransaction, 'amount'),
                        'external_id' => Arr::get($sourceTransaction, 'invoice_id'),
                    ]);
                }

                $nextBillingHistory = Arr::get($response, 'links.pages.next');
            }
        } catch (RequestException $e) {
            Log::info($e->getMessage());

            return Collection::make();
        }

        return Collection::make($sourceTransactions);
    }

    /**
     * Save model to the repository.
     *
     * @param Model $model
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
     * @param Model $model
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
     * @param Model $model
     * @param array $attributes
     * @return bool
     */
    public function update(Model $model, array $attributes): bool
    {
        // Billing API is not writable
        return false;
    }
}
