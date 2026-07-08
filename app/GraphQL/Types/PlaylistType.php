<?php

declare(strict_types=1);

namespace App\GraphQL\Types;

use App\Models\List\Playlist;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Str;

class PlaylistType
{
    public function resolveSiteUrlAttribute(Playlist $playlist): string
    {
        return Str::of(Config::get('app.url'))
            ->append('/playlist/')
            ->append($playlist->hashid)
            ->__toString();
    }
}
