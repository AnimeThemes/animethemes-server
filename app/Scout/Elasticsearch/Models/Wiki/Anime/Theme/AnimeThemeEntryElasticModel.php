<?php

declare(strict_types=1);

namespace App\Scout\Elasticsearch\Models\Wiki\Anime\Theme;

use App\Models\Wiki\Anime\Theme\AnimeThemeEntry;
use Illuminate\Support\Str;

class AnimeThemeEntryElasticModel
{
    /**
     * @return array<string, mixed>
     */
    public static function toSearchableArray(AnimeThemeEntry $entry): array
    {
        return [
            ...$entry->attributesToArray(),
            'theme' => $entry->animetheme->toSearchableArray(),
            'version' => Str::of(strval($entry->version))->prepend('v')->__toString(),
        ];
    }
}
