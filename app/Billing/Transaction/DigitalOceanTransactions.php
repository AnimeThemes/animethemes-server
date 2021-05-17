<?php

namespace App\Billing\Transaction;

use App\Contracts\Billing\ServiceTransactions;
use App\Enums\BillingService;
use App\Models\Transaction;
use Carbon\Carbon;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Exception\ServerException;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Log;

class DigitalOceanTransactions implements ServiceTransactions
{
    /**
     * Collect transactions from billing service.
     *
     * @return \Illuminate\Support\Collection
     */
    public function getTransactions()
    {
        // Do not proceed if we do not have authorization to the DO API
        $do_bearer_token = Config::get('services.do.token');
        if ($do_bearer_token === null) {
            Log::error('DO_BEARER_TOKEN must be configured in your env file.');

            return Collection::make();
        }

        $source_transactions = [];

        try {
            $client = new Client;

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
                        'date' => Carbon::parse($source_transaction['date']),
                        'service' => BillingService::DIGITALOCEAN,
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
}
