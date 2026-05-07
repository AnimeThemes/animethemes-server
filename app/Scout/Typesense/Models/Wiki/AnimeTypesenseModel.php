<?php

declare(strict_types=1);

namespace App\Scout\Typesense\Models\Wiki;

use App\Models\Wiki\Anime;
use App\Models\Wiki\Synonym;

class AnimeTypesenseModel
{
    /**
     * @return array<string, mixed>
     */
    public static function toSearchableArray(Anime $anime): array
    {
        return [
            'id' => (string) $anime->getKey(),
            'format' => $anime->format?->value,
            'name' => $anime->name,
            'season' => $anime->season?->value,
            'year' => $anime->year,
            'created_at' => $anime->created_at?->timestamp,
            'updated_at' => $anime->updated_at?->timestamp,
            'synonyms' => $anime->synonyms->map(fn (Synonym $synonym) => $synonym->text)->all(),
        ];
    }
}
