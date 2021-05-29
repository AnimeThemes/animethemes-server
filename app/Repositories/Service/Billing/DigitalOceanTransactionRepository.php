<?php

namespace App\Repositories\Service\Billing;

use App\Contracts\Repositories\Repository;
use App\Enums\Billing\Service;
use App\Enums\Filter\AllowedDateFormat;
use App\Models\Billing\Transaction;
use DateTime;
use DateTimeInterface;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Exception\ServerException;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Log;

class DigitalOceanTransactionRepository implements Repository
{
    /**
     * Get all models from the repository.
     *
     * @return \Illuminate\Support\Collection
     */
    public function all()
    {
        // Do not proceed if we do not have authorization to the DO API
        $do_bearer_token = Config::get('services.do.token');
        if ($do_bearer_token === null) {
            Log::error('DO_BEARER_TOKEN must be configured in your env file.');

            return Collection::make();
        }

        $source_transactions = [];

        try {
            $client = new Client();

            $next_billing_history = 'https://api.digitalocean.com/v2/customers/my/billing_history?per_page=200';
            while (! empty($next_billing_history)) {
                // Try not to upset DO
                sleep(rand(5, 15));

                $response = $client->get($next_billing_history, [
                    'headers' => [
                        'Content-Type' => 'application/json',
                        'Authorization' => 'Bearer '.$do_bearer_token,
                    ],
                ]);

                $billing_history_json = json_decode($response->getBody()->getContents(), true);

                $billing_history = $billing_history_json['billing_history'];
                foreach ($billing_history as $source_transaction) {
                    $source_transactions[] = Transaction::make([
                        'date' => DateTime::createFromFormat('!'.DateTimeInterface::RFC3339, $source_transaction['date'])->format(AllowedDateFormat::WITH_DAY),
                        'service' => Service::DIGITALOCEAN,
                        'description' => $source_transaction['description'],
                        'amount' => $source_transaction['amount'],
                        'external_id' => Arr::get($source_transaction, 'invoice_id', null),
                    ]);
                }

                $next_billing_history = Arr::get($billing_history_json, 'links.pages.next', null);
            }
        } catch (ClientException $e) {
            Log::info($e->getMessage());

            return Collection::make();
        } catch (ServerException $e) {
            Log::info($e->getMessage());

            return Collection::make();
        }

        return Collection::make($source_transactions);
    }

    /**
     * Save model to the repository.
     *
     * @param Model $model
     * @return bool
     */
    public function save(Model $model)
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
    public function delete(Model $model)
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
    public function update(Model $model, array $attributes)
    {
        // Billing API is not writable
        return false;
    }
}
