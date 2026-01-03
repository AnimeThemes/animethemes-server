<?php

declare(strict_types=1);

namespace App\Actions\Models\List\External\Token\Site;

use App\Actions\Models\List\External\Token\BaseExternalTokenAction;
use App\Constants\Config\ServiceConstants;
use App\Models\List\External\ExternalToken;
use Exception;
use Illuminate\Http\Client\RequestException;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class MalExternalTokenAction extends BaseExternalTokenAction
{
    /**
     * Use the authorization code to get the tokens and store them.
     *
     * @param  array{code: string, state: string}  $parameters
     *
     * @throws Exception
     */
    public function store(array $parameters): ExternalToken
    {
        $code = Arr::string($parameters, 'code');
        $state = Arr::string($parameters, 'state');

        $codeVerifier = Cache::get("mal-external-token-request-{$state}");

        try {
            $response = Http::asForm()
                ->acceptJson()
                ->post('https://myanimelist.net/v1/oauth2/token', [
                    'grant_type' => 'authorization_code',
                    'client_id' => Config::string(ServiceConstants::MAL_CLIENT_ID),
                    'client_secret' => Config::string(ServiceConstants::MAL_CLIENT_SECRET),
                    'redirect_uri' => Config::string(ServiceConstants::MAL_REDIRECT_URI),
                    'code' => $code,
                    'code_verifier' => $codeVerifier,
                ])
                ->throw()
                ->json();

            $token = Arr::string($response, 'access_token');
            $refreshToken = Arr::string($response, 'refresh_token');

            return ExternalToken::query()->create([
                ExternalToken::ATTRIBUTE_ACCESS_TOKEN => Crypt::encrypt($token),
                ExternalToken::ATTRIBUTE_REFRESH_TOKEN => Crypt::encrypt($refreshToken),
            ]);
        } catch (RequestException $e) {
            Log::error($e->getMessage());

            throw $e;
        } finally {
            Cache::forget("mal-external-token-request-{$state}");
        }
    }
}
