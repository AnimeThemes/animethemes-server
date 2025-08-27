<?php

declare(strict_types=1);

namespace App\Enums\Models\List;

use App\Concerns\Enums\LocalizesName;
use App\Constants\Config\ServiceConstants;
use App\Enums\Models\Wiki\ResourceSite;
use Filament\Support\Contracts\HasLabel;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Str;
use Illuminate\Support\Uri;

enum ExternalProfileSite: int implements HasLabel
{
    use LocalizesName;

    case MAL = 0;
    case ANILIST = 1;
    case KITSU = 2;

    /**
     * Get the ResourceSite by the ExternalProfileSite value.
     */
    public function getResourceSite(): ResourceSite
    {
        return match ($this) {
            self::MAL => ResourceSite::MAL,
            self::ANILIST => ResourceSite::ANILIST,
            self::KITSU => ResourceSite::KITSU,
        };
    }

    /**
     * Get the link of the external site to authenticate the user.
     */
    public function getAuthorizeUrl(): ?Uri
    {
        if ($this === self::MAL) {
            $codeVerifier = bin2hex(random_bytes(64));

            $id = Str::uuid()->__toString();

            Cache::set("mal-external-token-request-{$id}", $codeVerifier);

            return Uri::of('https://myanimelist.net/v1/oauth2/authorize')
                ->withQuery([
                    'client_id' => Config::get(ServiceConstants::MAL_CLIENT_ID),
                    'redirect_uri' => Config::get(ServiceConstants::MAL_REDIRECT_URI),
                    'code_challenge' => $codeVerifier,
                    'state' => $id,
                    'response_type' => 'code',
                    'code_challenge_method' => 'plain',
                ]);
        }

        if ($this === self::ANILIST) {
            return Uri::of('https://anilist.co/api/v2/oauth/authorize')
                ->withQuery([
                    'client_id' => Config::get(ServiceConstants::ANILIST_CLIENT_ID),
                    'redirect_uri' => Config::get(ServiceConstants::ANILIST_REDIRECT_URI),
                    'response_type' => 'code',
                ]);
        }

        return null;
    }
}
