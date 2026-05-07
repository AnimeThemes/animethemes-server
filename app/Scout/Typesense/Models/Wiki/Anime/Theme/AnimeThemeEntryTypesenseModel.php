<?php

declare(strict_types=1);

namespace App\Scout\Typesense\Models\Wiki\Anime\Theme;

use App\Models\Wiki\Anime\Theme\AnimeThemeEntry;
use Illuminate\Support\Str;

class AnimeThemeEntryTypesenseModel
{
    /**
     * @return array<string, mixed>
     */
    public static function toSearchableArray(AnimeThemeEntry $entry): array
    {
        return [
            'id' => (string) $entry->getKey(),
            'animetheme' => $entry->animetheme->toSearchableArray(),
            'version' => $version = Str::of(strval($entry->version))->prepend('v')->__toString(),
            'type_sequence_version' => $entry->animetheme->type->localize().(($entry->animetheme->sequence ?? 1)).$version,
        ];
    }
}
