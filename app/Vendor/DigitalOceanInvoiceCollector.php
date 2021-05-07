<?php

namespace App\Vendor;

use App\Enums\InvoiceVendor;
use App\Models\Invoice;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Exception\ServerException;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Log;

class DigitalOceanInvoiceCollector extends VendorInvoiceCollector
{
    /**
     * Collect invoices from vendor.
     *
     * @return \Illuminate\Support\Collection
     */
    public function getInvoices()
    {
        // Do not proceed if we do not have authorization to the DO API
        $do_bearer_token = Config::get('services.do.token');
        if ($do_bearer_token === null) {
            Log::error('DO_BEARER_TOKEN must be configured in your env file.');

            return Collection::make();
        }

        $source_invoices = [];

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
                foreach ($billing_history as $source_invoice) {
                    if ($source_invoice['type'] === 'Invoice') {
                        $source_invoices[] = Invoice::make([
                            'vendor' => InvoiceVendor::DIGITALOCEAN,
                            'description' => $source_invoice['description'],
                            'amount' => $source_invoice['amount'],
                            'external_id' => $source_invoice['invoice_id'],
                        ]);
                    }
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

        return Collection::make($source_invoices);
    }
}
