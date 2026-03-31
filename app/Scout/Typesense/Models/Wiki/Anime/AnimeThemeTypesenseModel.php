<?php

declare(strict_types=1);

namespace App\Scout\Typesense\Models\Wiki\Anime;

use App\Models\Wiki\Anime\AnimeTheme;

class AnimeThemeTypesenseModel
{
    /**
     * @return array<string, mixed>
     */
    public static function toSearchableArray(AnimeTheme $theme): array
    {
        return [
            'id' => (string) $theme->getKey(),
            'created_at' => $theme->created_at?->timestamp,

            'type_sequence' => $theme->type->localize().($theme->sequence ?? 1),
            'type' => $theme->type->localize(),
            'sequence' => (string) ($theme->sequence ?? 1),

            'anime' => $theme->anime->toSearchableArray(),
            'song' => $theme->song?->toSearchableArray(),
        ];
    }
}
