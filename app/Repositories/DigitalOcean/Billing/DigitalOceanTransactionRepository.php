<?php

declare(strict_types=1);

namespace App\Repositories\DigitalOcean\Billing;

use App\Enums\Http\Api\Filter\AllowedDateFormat;
use App\Enums\Models\Billing\Service;
use App\Models\Billing\Transaction;
use App\Repositories\DigitalOcean\DigitalOceanRepository;
use DateTime;
use DateTimeInterface;
use Illuminate\Http\Client\RequestException;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Sleep;

/**
 * Class DigitalOceanTransactionRepository.
 *
 * @extends DigitalOceanRepository<Transaction>
 */
class DigitalOceanTransactionRepository extends DigitalOceanRepository
{
    /**
     * Get models from the repository.
     *
     * @param  array  $columns
     * @return Collection<int, Transaction>
     *
     * @throws RequestException
     */
    public function get(array $columns = ['*']): Collection
    {
        $sourceTransactions = [];

        $request = Http::withToken(Config::get('services.do.token'))->contentType('application/json');

        $nextBillingHistory = 'https://api.digitalocean.com/v2/customers/my/billing_history?per_page=200';
        while (! empty($nextBillingHistory)) {
            // Try not to upset DO
            Sleep::for(rand(2, 5))->second();

            $response = $request->get($nextBillingHistory)->throw()->json();

            $billingHistory = Arr::get($response, 'billing_history', []);
            foreach ($billingHistory as $sourceTransaction) {
                $date = DateTime::createFromFormat(
                    '!'.DateTimeInterface::RFC3339,
                    Arr::get($sourceTransaction, 'date')
                );

                $sourceTransactions[] = new Transaction([
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
}
