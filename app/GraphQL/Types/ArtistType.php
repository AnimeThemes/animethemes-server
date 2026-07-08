<?php

declare(strict_types=1);

namespace App\GraphQL\Types;

use App\Models\Wiki\Artist;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Str;

class ArtistType
{
    public function resolveSiteUrlAttribute(Artist $artist): string
    {
        return Str::of(Config::get('app.url'))
            ->append('/artist/')
            ->append($artist->slug)
            ->__toString();
    }
}
