<?php

declare(strict_types=1);

namespace App\Actions\Models\List\External\Token\Site;

use App\Actions\Models\List\External\Token\BaseExternalTokenAction;
use App\Models\List\External\ExternalToken;
use Error;
use Exception;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class AnilistExternalTokenAction extends BaseExternalTokenAction
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

        try {
            $response = Http::acceptJson()
                ->asForm()
                ->post('https://anilist.co/api/v2/oauth/token', [
                    'grant_type' => 'authorization_code',
                    'client_id' => Config::get('services.anilist.client_id'),
                    'client_secret' => Config::get('services.anilist.client_secret'),
                    'redirect_uri' => Config::get('services.anilist.redirect_uri'),
                    'code' => $code,
                ])
                ->throw()
                ->json();

            $token = Arr::get($response, 'access_token');

            if ($token === null) {
                throw new Error('Failed to get token');
            }

            return ExternalToken::query()->create([
                ExternalToken::ATTRIBUTE_ACCESS_TOKEN => Crypt::encrypt($token),
            ]);
        } catch (Exception $e) {
            Log::error($e->getMessage());

            throw $e;
        }
    }
}
