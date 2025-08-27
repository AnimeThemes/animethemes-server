<?php

declare(strict_types=1);

namespace App\Actions\Models\List\External\Token\Site;

use App\Actions\Models\List\External\Token\BaseExternalTokenAction;
use App\Constants\Config\ServiceConstants;
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
     *
     * @throws Exception
     */
    public function store(array $parameters): ExternalToken
    {
        $code = Arr::get($parameters, 'code');
        $state = Arr::get($parameters, 'state');

        $codeVerifier = Cache::get("mal-external-token-request-{$state}");

        try {
            $response = Http::asForm()
                ->post('https://myanimelist.net/v1/oauth2/token', [
                    'grant_type' => 'authorization_code',
                    'client_id' => Config::get(ServiceConstants::MAL_CLIENT_ID),
                    'client_secret' => Config::get(ServiceConstants::MAL_CLIENT_SECRET),
                    'redirect_uri' => Config::get(ServiceConstants::MAL_REDIRECT_URI),
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
        } catch (Exception $e) {
            Log::error($e->getMessage());

            throw $e;
        } finally {
            Cache::forget("mal-external-token-request-{$state}");
        }

        return ExternalToken::query()->create([
            ExternalToken::ATTRIBUTE_ACCESS_TOKEN => Crypt::encrypt($token),
            ExternalToken::ATTRIBUTE_REFRESH_TOKEN => Crypt::encrypt($refreshToken),
        ]);
    }
}
