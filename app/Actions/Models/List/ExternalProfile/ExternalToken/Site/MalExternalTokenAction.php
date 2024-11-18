<?php

declare(strict_types=1);

namespace App\Actions\Models\List\ExternalProfile\ExternalToken\Site;

use App\Actions\Models\List\ExternalProfile\ExternalToken\BaseExternalTokenAction;
use App\Models\List\External\ExternalToken;
use Error;
use Exception;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

/**
 * Class MalExternalTokenAction.
 */
class MalExternalTokenAction extends BaseExternalTokenAction
{
    /**
     * Use the authorization code to get the tokens and store them.
     *
     * @param  array  $parameters
     * @return ExternalToken
     *
     * @throws Exception
     */
    public function store(array $parameters): ExternalToken
    {
        $code = Arr::get($parameters, 'code');
        $state = Arr::get($parameters, 'state');

        $codeVerifier = Cache::get("mal-external-token-request-{$state}");

        Cache::forget("mal-external-token-request-{$state}");

        try {
            $response = Http::asForm()
                ->post('https://myanimelist.net/v1/oauth2/token', [
                    'grant_type' => 'authorization_code',
                    'client_id' => Config::get('services.mal.client_id'),
                    'client_secret' => Config::get('services.mal.client_secret'),
                    'redirect_uri' => Config::get('services.mal.redirect_uri'),
                    'code' => $code,
                    'code_verifier' => $codeVerifier,
                ])
                ->throw()
                ->json();

            $token = Arr::get($response, 'access_token');
            $refreshToken = Arr::get($response, 'refresh_token');

            if ($token === null) {
                throw new Error('Failed to get token');
            }

            return ExternalToken::query()->create([
                ExternalToken::ATTRIBUTE_ACCESS_TOKEN => Crypt::encrypt($token),
                ExternalToken::ATTRIBUTE_REFRESH_TOKEN => Crypt::encrypt($refreshToken),
            ]);
        } catch (Exception $e) {
            Log::error($e->getMessage());

            throw $e;
        }
    }
}
