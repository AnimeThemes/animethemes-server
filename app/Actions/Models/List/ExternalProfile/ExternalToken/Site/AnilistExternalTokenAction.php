<?php

declare(strict_types=1);

namespace App\Actions\Models\List\ExternalProfile\ExternalToken\Site;

use App\Actions\Models\List\ExternalProfile\ExternalToken\BaseExternalTokenAction;
use App\Models\List\External\ExternalToken;
use Illuminate\Http\Client\RequestException;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

/**
 * Class AnilistExternalTokenAction.
 */
class AnilistExternalTokenAction extends BaseExternalTokenAction
{
    /**
     * Use the authorization code to get the tokens and store them.
     *
     * @param  string  $code
     * @return ExternalToken|null
     */
    public function store(string $code): ?ExternalToken
    {
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

            if ($token !== null) {
                return ExternalToken::query()->create([
                    ExternalToken::ATTRIBUTE_ACCESS_TOKEN => $token,
                ]);
            }

            return null;

        } catch (RequestException $e) {
            Log::error($e->getMessage());

            throw $e;
        }
    }
}
