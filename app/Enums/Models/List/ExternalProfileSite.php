<?php

declare(strict_types=1);

namespace App\Enums\Models\List;

use App\Concerns\Enums\LocalizesName;
use App\Enums\Models\Wiki\ResourceSite;
use Filament\Support\Contracts\HasLabel;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Str;

/**
 * Enum ExternalProfileSite.
 */
enum ExternalProfileSite: int implements HasLabel
{
    use LocalizesName;

    case MAL = 0;
    case ANILIST = 1;
    case KITSU = 2;

    /**
     * Get the ResourceSite by the ExternalProfileSite value.
     *
     * @return ResourceSite
     */
    public function getResourceSite(): ResourceSite
    {
        return match ($this) {
            static::MAL => ResourceSite::MAL,
            static::ANILIST => ResourceSite::ANILIST,
            static::KITSU => ResourceSite::KITSU,
        };
    }

    /**
     * Get the link of the external site to authenticate the user.
     *
     * @return string|null
     */
    public function getAuthorizeUrl(): ?string
    {
        if ($this === static::MAL) {
            $codeVerifier = bin2hex(random_bytes(64));

            $id = Str::uuid()->__toString();

            Cache::set("mal-external-token-request-{$id}", $codeVerifier);

            $query = [
                'client_id' => Config::get('services.mal.client_id'),
                'redirect_uri' => Config::get('services.mal.redirect_uri'),
                'code_challenge' => $codeVerifier,
                'state' => $id,
                'response_type' => 'code',
                'code_challenge_method' => 'plain',
            ];

            return 'https://myanimelist.net/v1/oauth2/authorize?' . http_build_query($query);
        }

        if ($this === static::ANILIST) {
            $query = [
                'client_id' => Config::get('services.anilist.client_id'),
                'redirect_uri' => Config::get('services.anilist.redirect_uri'),
                'response_type' => 'code',
            ];

            return 'https://anilist.co/api/v2/oauth/authorize?' . http_build_query($query);
        }

        return null;
    }
}
