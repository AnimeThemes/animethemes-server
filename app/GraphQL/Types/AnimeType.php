<?php

declare(strict_types=1);

namespace App\GraphQL\Types;

use App\Models\Wiki\Anime;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Str;

class AnimeType
{
    /**
     * @return array<string, string>
     */
    public function resolveTitleAttribute(Anime $anime): array
    {
        return [
            'romaji' => $anime->title,
            'english' => $anime->title_english,
            'native' => $anime->title_native,
        ];
    }

    public function resolveSiteUrlAttribute(Anime $anime): string
    {
        return Str::of(Config::get('app.url'))
            ->append('/anime/')
            ->append($anime->slug)
            ->__toString();
    }
}
