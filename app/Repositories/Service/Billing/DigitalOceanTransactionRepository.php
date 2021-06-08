<?php

declare(strict_types=1);

namespace App\Repositories\Service\Billing;

use App\Contracts\Repositories\Repository;
use App\Enums\Billing\Service;
use App\Enums\Http\Api\Filter\AllowedDateFormat;
use App\Models\Billing\Transaction;
use DateTime;
use DateTimeInterface;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Exception\ServerException;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Log;

/**
 * Class DigitalOceanTransactionRepository.
 */
class DigitalOceanTransactionRepository implements Repository
{
    /**
     * Get all models from the repository.
     *
     * @return Collection
     */
    public function all(): Collection
    {
        // Do not proceed if we do not have authorization to the DO API
        $doBearerToken = Config::get('services.do.token');
        if ($doBearerToken === null) {
            Log::error('DO_BEARER_TOKEN must be configured in your env file.');

            return Collection::make();
        }

        $sourceTransactions = [];

        try {
            $client = new Client();

            $nextBillingHistory = 'https://api.digitalocean.com/v2/customers/my/billing_history?per_page=200';
            while (! empty($nextBillingHistory)) {
                // Try not to upset DO
                sleep(rand(5, 15));

                $response = $client->get($nextBillingHistory, [
                    'headers' => [
                        'Content-Type' => 'application/json',
                        'Authorization' => 'Bearer '.$doBearerToken,
                    ],
                ]);

                $billingHistoryJson = json_decode($response->getBody()->getContents(), true);

                $billingHistory = Arr::get($billingHistoryJson, 'billing_history', []);
                foreach ($billingHistory as $sourceTransaction) {
                    $sourceTransactions[] = Transaction::make([
                        'date' => DateTime::createFromFormat('!'.DateTimeInterface::RFC3339, Arr::get($sourceTransaction, 'date'))->format(AllowedDateFormat::YMD),
                        'service' => Service::DIGITALOCEAN,
                        'description' => Arr::get($sourceTransaction, 'description'),
                        'amount' => Arr::get($sourceTransaction, 'amount'),
                        'external_id' => Arr::get($sourceTransaction, 'invoice_id'),
                    ]);
                }

                $nextBillingHistory = Arr::get($billingHistoryJson, 'links.pages.next');
            }
        } catch (ClientException | ServerException | GuzzleException $e) {
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
