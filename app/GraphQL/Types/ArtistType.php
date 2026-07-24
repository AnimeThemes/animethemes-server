<?php

declare(strict_types=1);

namespace App\GraphQL\Types;

use App\Models\Wiki\Artist;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Str;

class ArtistType
{
    /**
     * @return array<string, string>
     */
    public function resolveNameAttribute(Artist $artist): array
    {
        return [
            'main' => $artist->name,
            'native' => $artist->name_native,
        ];
    }

    public function resolveSiteUrlAttribute(Artist $artist): string
    {
        return Str::of(Config::get('app.url'))
            ->append('/artist/')
            ->append($artist->slug)
            ->__toString();
    }
}
