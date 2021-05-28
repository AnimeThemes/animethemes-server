<?php

namespace App\Repositories\Service\Billing;

use App\Contracts\Repositories\Repository;
use App\Enums\Billing\Frequency;
use App\Enums\Billing\Service;
use App\Enums\Filter\AllowedDateFormat;
use App\Models\Billing\Balance;
use Carbon\Carbon;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Exception\ServerException;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Log;

class DigitalOceanBalanceRepository implements Repository
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

        try {
            $client = new Client();

            $response = $client->get('https://api.digitalocean.com/v2/customers/my/balance', [
                'headers' => [
                    'Content-Type' => 'application/json',
                    'Authorization' => 'Bearer '.$do_bearer_token,
                ],
            ]);

            $balance_json = json_decode($response->getBody()->getContents(), true);

            $balance = Balance::make([
                'date' => Carbon::now()->firstOfMonth()->format(AllowedDateFormat::WITH_DAY),
                'service' => Service::DIGITALOCEAN,
                'frequency' => Frequency::MONTHLY,
                'usage' => $balance_json['month_to_date_usage'],
                'balance' => -1 * floatval($balance_json['month_to_date_balance']),
            ]);

            return collect([$balance]);
        } catch (ClientException $e) {
            Log::info($e->getMessage());

            return Collection::make();
        } catch (ServerException $e) {
            Log::info($e->getMessage());

            return Collection::make();
        }
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
