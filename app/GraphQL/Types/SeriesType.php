<?php

declare(strict_types=1);

namespace App\GraphQL\Types;

use App\Models\Wiki\Series;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Str;

class SeriesType
{
    /**
     * @return array<string, string>
     */
    public function resolveTitleAttribute(Series $series): array
    {
        return [
            'romaji' => $series->title,
        ];
    }

    public function resolveSiteUrlAttribute(Series $series): string
    {
        return Str::of(Config::get('app.url'))
            ->append('/series/')
            ->append($series->slug)
            ->__toString();
    }
}
